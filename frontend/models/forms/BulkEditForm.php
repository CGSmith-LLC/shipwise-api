<?php

namespace frontend\models\forms;

use yii\base\Model;

/**
 * Class BulkEditForm
 *
 * This model is for modifying multiple orders
 *
 * @package frontend\models\forms
 */
class BulkEditForm extends Model
{

    /** @var array of Order items */
    public $orders = [];

    /** @var string User input that needs to be parsed into order identifiers*/
    public $customer;

    /** @var string Action to be performed */
    public $action;

    /** @var string A delineated list of order reference numbers */
    public $order_ids;

    public $open_date;
    public $reopen_enable = false;

    public $confirmed = false;


    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['customer', 'action', 'order_ids'], 'required'],
            [['confirmed', 'reopen_enable'], 'boolean'],
            [['open_date'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'order_ids' => 'Order numbers',
            'action' => 'Status to change to',
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        // Break by ; , newline or space
        $pattern = '/[;,\r\n ]/';
        $this->orders = preg_split($pattern, $this->order_ids);

        return parent::beforeValidate();
    }
}
