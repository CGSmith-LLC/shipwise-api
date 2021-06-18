<?php

namespace frontend\models;

use common\models\base\BaseBatch;
use common\models\base\BaseBatchItem;
use common\models\BulkItem;
use common\models\Status;

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
 * @property array $params
 * @property array $orderIDs
 */
class BulkAction extends BaseBulkAction
{

    const EXECUTION_TYPE_SYNC = 1;
    const EXECUTION_TYPE_ASYNC = 2;

    const ACTION_CHANGE_STATUS = 'changeStatus';
    const ACTION_PACKING_SLIPS = 'packingSlips';
    const ACTION_SHIPPING_LABELS = 'shippingLabels';
    const ACTION_SHIPPING_LABELS_PACKING_SLIPS = 'shippingLabelsPackingSlips';
    const ACTION_BATCH_CREATE = 'createNewBatch';
    const ACTION_BATCH_ADD = 'addToExistingBatch';

    /**
     * List of all available actions
     *
     * @var array
     */
    public static $actionList = [
        self::ACTION_CHANGE_STATUS,
        self::ACTION_PACKING_SLIPS,
        self::ACTION_SHIPPING_LABELS,
        self::ACTION_SHIPPING_LABELS_PACKING_SLIPS,
        self::ACTION_BATCH_CREATE,
        self::ACTION_BATCH_ADD,
    ];

    /**
     * List of printing actions
     * @return array
     */
    public static function getPrintActionsList()
    {
        return [
            self::ACTION_PACKING_SLIPS => static::readable(BulkAction::ACTION_PACKING_SLIPS),
            self::ACTION_SHIPPING_LABELS => static::readable(BulkAction::ACTION_SHIPPING_LABELS),
            self::ACTION_SHIPPING_LABELS_PACKING_SLIPS => static::readable(BulkAction::ACTION_SHIPPING_LABELS_PACKING_SLIPS),
        ];
    }

    /**
     * List of batch actions
     * @return array
     */
    public static function getBatchActionsList()
    {
        return [
            self::ACTION_BATCH_CREATE => static::readable(BulkAction::ACTION_BATCH_CREATE),
            self::ACTION_BATCH_ADD => static::readable(BulkAction::ACTION_BATCH_ADD),
        ];
    }

    /**
     * Returns human readable string
     *
     * @param string $str
     *
     * @return string
     */
    public static function readable($str)
    {
        $split = preg_split('/(?=[A-Z])/', $str);
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
     * Additional action options
     *
     * @var array
     */
    public $options = [];

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
            ['options', 'safe'],
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
        $exploded = explode("_", $this->$attribute);
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

    private function addToExistingBatch($params = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nbUpdated = 0;
            $batch = BaseBatch::find()->where(['id' => $this->options['batch_id']])->one();

            foreach ($this->orderIDs as $id) {
                if (($order = Order::findOne($id)) !== null) {
                    $batchItem = new BaseBatchItem();
                    $batchItem->setAttributes([
                        'order_id' => $order->id,
                        'batch_id' => $batch->id,
                    ]);
                    $batchItem->save();
                    $nbUpdated++;
                } else {
                    Yii::warning("Order with ID $id not found.");
                }
            }
            $transaction->commit();
            $this->_success = true;
            $this->_message = ($nbUpdated > 0) ? "$nbUpdated orders assigned to batch " . $batch->name . "." : "";

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;
        }

        return self::EXECUTION_TYPE_SYNC;
    }

    private function createNewBatch($params = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nbUpdated = 0;
            $batch = new BaseBatch();
            $batch->setAttributes([
                'name' => $this->options['batch_name'],
                'customer_id' => Yii::$app->user->identity->customer_id,
            ]);
            $batch->validate();
            $batch->save();

            foreach ($this->orderIDs as $id) {
                if (($order = Order::findOne($id)) !== null) {
                    $batchItem = new BaseBatchItem();
                    $batchItem->setAttributes([
                        'order_id' => $order->id,
                        'batch_id' => $batch->id,
                    ]);
                    $batchItem->save();
                    $nbUpdated++;
                } else {
                    Yii::warning("Order with ID $id not found.");
                }
            }
            $transaction->commit();
            $this->_success = true;
            $this->_message = ($nbUpdated > 0) ? "$nbUpdated orders assigned to batch " . $batch->name . "." : "";

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;
        }

        return self::EXECUTION_TYPE_SYNC;
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
    public function changeStatus($params = null)
    {
        Yii::debug($params);
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
        $bulkAction = $this->createBulkAction();

        return $this->asyncExecute($bulkAction, 'GeneratePackingSlipJob', $params);
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
        $bulkAction = $this->createBulkAction();

        return $this->asyncExecute($bulkAction, 'CreateShippingLabelJob', $params);
    }

    /**
     * Print packing slips AND Print shipping labels
     *
     * Triggers creation of packing slips for each order.
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
    private function shippingLabelsPackingSlips($params = null)
    {
        $bulkAction = $this->createBulkAction();

        $job1 = $this->asyncExecute($bulkAction, 'CreateShippingLabelJob', $params);
        $job2 = $this->asyncExecute($bulkAction, 'GeneratePackingSlipJob', $params);

        return ($job1 && $job2) ? self::EXECUTION_TYPE_ASYNC : false;
    }

    /**
     * Bulk Action instance to track process.
     *
     * @return BaseBulkAction
     */
    private function createBulkAction()
    {
        $bulkAction = new BaseBulkAction();
        $bulkAction->code = $this->action;
        $bulkAction->name = self::readable($this->action);
        if (in_array($this->action, array_keys(static::getPrintActionsList()))) {
            $bulkAction->print_mode = $this->options['print_as_pdf'] ? static::PRINT_MODE_PDF : static::PRINT_MODE_QZ;
        }
        if (!$bulkAction->save()) {
            Yii::warning("Failed to save Bulk Action.");
            Yii::warning($bulkAction->attributes);
            Yii::warning($bulkAction->getErrors());
        }
        return $bulkAction;
    }

    /**
     * This function will execute orders asynchronously by adding them to a queue to be executed by job workers in
     * background.
     * Use Bulk Action to track process.
     *
     * @param BaseBulkAction $bulkAction
     * @param string $jobClassName The class name of the console job to be added to the queue
     *                                     eg. `GeneratePackingSlipJob`
     * @param array|null $params Optional
     *
     * @return bool|int False on failure, Integer on success
     */
    private function asyncExecute($bulkAction, $jobClassName, $params = null)
    {
        $jobClass = "\\console\\jobs\\{$jobClassName}";

        if (!class_exists($jobClass)) {
            $this->addError('action', 'Job class not found');
            return false;
        }

        try {
            $nbQueued = 0;
            foreach ($this->orderIDs as $id) {

                if (($order = Order::findOne($id)) !== null) {

                    $bulkItem = new BulkItem();

                    $bulkItem->bulk_action_id = $bulkAction->id;
                    $bulkItem->order_id = $order->id;
                    $bulkItem->job = $jobClassName;

                    if (!$bulkItem->save()) {
                        Yii::warning("Failed to save Bulk Action.");
                        Yii::warning($bulkItem->attributes);
                        Yii::warning($bulkItem->getErrors());
                    }

                    // Add to the execution queue
                    $queueMessageId = Yii::$app->queue->push(new $jobClass([
                        'orderId' => $id,
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
            $this->_link = Url::toRoute(['/order/bulk-result', 'id' => $bulkAction->id], true);

        } catch (\Exception $e) {
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;
        }

        return self::EXECUTION_TYPE_ASYNC;
    }

}