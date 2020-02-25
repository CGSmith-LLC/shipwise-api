<?php

namespace frontend\models;

use common\models\BulkItem;
use common\models\Status; // Order Status
use common\models\BulkAction as BaseBulkAction;
use Yii;
use yii\helpers\Url;

/**
 * BulkAction allows you to execute an action on multiple orders.
 *
 * Two types of executing exist:
 *  1. Bulk orders are immediately executed in a synchronous way.
 *  2. Bulk orders are added to a queue to be executed asynchronously in the background using jobs.
 *
 * @property string $action
 * @property array  $params
 * @property array  $orderIDs
 */
class BulkAction extends BaseBulkAction
{

    const EXECUTION_TYPE_SYNC  = 1;
    const EXECUTION_TYPE_ASYNC = 2;

    const ACTION_CHANGE_STATUS                 = 'changeStatus';
    const ACTION_PACKING_SLIPS                 = 'packingSlips';
    const ACTION_SHIPPING_LABELS               = 'shippingLabels';
    const ACTION_SHIPPING_LABELS_PACKING_SLIPS = 'shippingLabelsPackingSlips';

    /**
     * List of available actions
     *
     * @var array
     */
    public static $actionList = [
        self::ACTION_CHANGE_STATUS,
        self::ACTION_PACKING_SLIPS,
        self::ACTION_SHIPPING_LABELS,
        self::ACTION_SHIPPING_LABELS_PACKING_SLIPS,
    ];

    /**
     * Returns human readable string
     *
     * @param string $str
     *
     * @return string
     */
    public static function readable($str)
    {
        $split   = preg_split('/(?=[A-Z])/', $str);
        $implode = implode(" ", $split);
        return ucfirst($implode);
    }

    /**
     * Action to perform
     *
     * @var string
     */
    public $action = '';

    /**
     * Optional parameters passed along with action
     *
     * @var array
     */
    public $params = [];

    /**
     * Order IDs
     *
     * @var array
     */
    public $orderIDs = [];

    /**
     * @var bool
     */
    protected $_success = false;

    /**
     * @var string
     */
    protected $_message = '';

    /**
     * @var string
     */
    protected $_link = '';

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['action', 'orderIDs'], 'required'],
            ['action', 'validateAction'],
            ['orderIDs', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * Validation rule for action
     *
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateAction($attribute, $params, $validator)
    {
        $exploded     = explode("_", $this->$attribute);
        $this->action = $exploded[0];
        $this->params = array_slice($exploded, 1);

        if (!in_array($this->action, self::$actionList)) {
            $this->addError($attribute, 'Unsupported action');
        }
    }

    /**
     * Execute the action
     *
     * @return bool
     */
    public function execute()
    {
        return $this->{$this->action}($this->params);
    }

    /**
     * Change order status.
     *
     * This function executes all orders synchronously and returns a result.
     *
     * @param array|null $params Contains new status value
     *
     * @return bool|int False on failure, Integer on success
     */
    private function changeStatus($params = null)
    {
        if (isset($params[0]) && in_array($params[0], Status::getList('id', 'id'))) {
            $newStatus = $params[0];
        } else {
            $this->addError('params', 'Incorrect parameters');
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nbUpdated = 0;
            foreach ($this->orderIDs as $id) {
                if (($order = Order::findOne($id)) !== null) {
                    if ($order->changeStatus($newStatus)) {
                        $nbUpdated++;
                    } else {
                        Yii::warning($order->getErrors());
                    }
                } else {
                    Yii::warning("Order with ID $id not found.");
                }
            }
            $transaction->commit();
            $this->_success = true;
            $this->_message = ($nbUpdated > 0) ? "$nbUpdated orders successfully updated." : "";

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;
        }

        return self::EXECUTION_TYPE_SYNC;
    }

    /**
     * Print packing slips
     *
     * Triggers creation of packing slips for each order.
     *
     * This function will execute orders asynchronously by adding them to a queue to be executed by job workers in
     * background.
     * A Bulk Action object is created to track process.
     *
     * @param array|null $params Optional
     *
     * @return bool|int False on failure, Integer on success
     */
    private function packingSlips($params = null)
    {
        return $this->asyncExecute('console\jobs\GeneratePackingSlipJob', $params);
    }

    /**
     * Print shipping labels
     *
     * Triggers creation of shipping label for each order.
     *
     * This function will execute orders asynchronously by adding them to a queue to be executed by job workers in
     * background.
     * A Bulk Action object is created to track process.
     *
     * @param array|null $params Optional
     *
     * @return bool|int False on failure, Integer on success
     */
    private function shippingLabels($params = null)
    {
        return $this->asyncExecute('console\jobs\CreateShippingLabelJob', $params);
    }

    /**
     * This function will execute orders asynchronously by adding them to a queue to be executed by job workers in
     * background.
     * A Bulk Action object is created to track process.
     *
     * @param string     $jobClass The class name of the job to be added to the queue
     *                             eg. `console\jobs\GeneratePackingSlipJob`
     * @param array|null $params   Optional
     *
     * @return bool|int False on failure, Integer on success
     */
    private function asyncExecute($jobClass, $params = null)
    {
        $bulkAction       = new parent();
        $bulkAction->code = $this->action;
        $bulkAction->name = self::readable($this->action);
        if (!$bulkAction->save()) {
            Yii::warning("Failed to save Bulk Action.");
            Yii::warning($bulkAction->attributes);
            Yii::warning($bulkAction->getErrors());
        }

        try {
            $nbQueued = 0;
            foreach ($this->orderIDs as $id) {

                if (($order = Order::findOne($id)) !== null) {

                    $bulkItem = new BulkItem();

                    $bulkItem->bulk_action_id = $bulkAction->id;
                    $bulkItem->order_id       = $order->id;

                    if (!$bulkItem->save()) {
                        Yii::warning("Failed to save Bulk Action.");
                        Yii::warning($bulkItem->attributes);
                        Yii::warning($bulkItem->getErrors());
                    }

                    // Add to the execution queue
                    $queueMessageId = Yii::$app->queue->push(new $jobClass([
                        'orderId'    => $id,
                        'bulkItemId' => $bulkItem->id,
                    ]));

                    $bulkItem->queue_id = $queueMessageId;
                    $bulkItem->save(false);

                    $nbQueued++;

                } else {
                    Yii::warning("Order with ID $id not found.");
                }
            }
            $this->_success = true;
            $this->_message = ($nbQueued > 0) ? "$nbQueued orders added to execution queue." : "";
            $this->_link    = Url::toRoute(['/order/bulk-result', 'id' => $bulkAction->id], true);

        } catch (\Exception $e) {
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;
        }

        return self::EXECUTION_TYPE_ASYNC;
    }

}


