<?php

namespace frontend\models;

use common\models\Status;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

//use console\jobs\CreatePackingSlipJob;

/**
 * OrderBulk allows you to execute an action on multiple orders.
 *
 * @property string $action
 * @property array  $params
 * @property array  $orderIDs
 */
class OrderBulk extends Model
{

    const STATUS_SUCCESS_QUEUED    = 1; // Successfully added to execution queue
    const STATUS_SUCCESS_IMMEDIATE = 2; // Successfully executed (no queue needed)

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
     * @var array List of available actions
     */
    public static $actionList = [
        'status', // changes order status
        'printPackingSlip', // generates packing slips
        'printShippingLabel', // creates shipping labels
        'printAll', // triggers printPackingSlip & printShippingLabel
    ];

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
     * Change order status
     *
     * @param array|null $params Contains new status value
     *
     * @return bool|int False on failure, Integer code on success
     */
    private function status($params = null)
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

        return self::STATUS_SUCCESS_IMMEDIATE;
    }

    /**
     * Print packing slips
     *
     * This function will trigger creation of packing slips for each order
     *
     * @param array|null $params Optional
     *
     * @return bool|int False on failure, Integer code on success
     */
    private function printPackingSlip($params = null)
    {
        try {
            $nbQueued = 0;
            foreach ($this->orderIDs as $id) {

                if (($order = Order::findOne($id)) !== null) {
                    // @todo Create CreatePackingSlipJob class
                    // Add to the execution queue
                    //Yii::$app->queue->push(new CreatePackingSlipJob(['orderId' => $id]));
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

        return self::STATUS_SUCCESS_QUEUED;
    }

}


