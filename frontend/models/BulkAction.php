<?php

namespace frontend\models;

use common\models\Status; // Order Status
use common\models\BulkAction as BaseBulkAction;
use Yii;
use yii\helpers\Url;

//use console\jobs\CreatePackingSlipJob;

/**
 * BulkAction allows you to execute an action on multiple orders.
 *
 * Two types of executing exist:
 *  1. Bulk orders are immediately executed in a sync way.
 *  2. Bulk orders are added to a queue to be executed async in background using jobs.
 *
 * @property string $action
 * @property array  $params
 * @property array  $orderIDs
 */
class BulkAction extends BaseBulkAction
{

    const EXECUTION_STATUS_DONE   = 1; // Successfully executed
    const EXECUTION_STATUS_QUEUED = 2; // Successfully added to a queue for execution

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
     * @return array
     */
    public static function readable($str)
    {
        $split   = preg_split('/(?=[A-Z])/', $str);
        $implode = implode(" ", $split);
        $result  = ucfirst($implode);

        return $result;
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
     * This function executes in a sync way. Immediate execution.
     *
     * @param array|null $params Contains new status value
     *
     * @return bool|int False on failure, Integer code on success
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

        return self::EXECUTION_STATUS_DONE;
    }

    /**
     * Print packing slips
     *
     * Trigger creation of packing slips for each order.
     *
     * This function executes in async way.
     * Bulk Action is created, order are added to a queue for background execution.
     *
     * @param array|null $params Optional
     *
     * @return bool|int False on failure, Integer code on success
     */
    private function packingSlips($params = null)
    {
        try {
            $nbQueued = 0;
            foreach ($this->orderIDs as $id) {

                if (($order = Order::findOne($id)) !== null) {
                    // @todo Create CreatePackingSlipJob class
                    // Add to the execution queue
                    // Yii::$app->queue->push(new CreatePackingSlipJob(['orderId' => $id]));
                    $nbQueued++;

                } else {
                    Yii::warning("Order with ID $id not found.");
                }
            }
            $this->_success = true;
            $this->_message = ($nbQueued > 0) ? "$nbQueued orders added to execution queue." : "";
            $this->_link    = Url::toRoute(['/order/batch', 'print' => 'packingSlip', 'id' => $this->orderIDs],
                true);

        } catch (\Exception $e) {
            $this->addError('action', 'Execution failed. ' . $e->getMessage());
            return false;

        }

        return self::EXECUTION_STATUS_QUEUED;
    }

}


