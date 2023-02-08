<?php

namespace frontend\models\forms;

use common\models\ScheduledOrder;
use yii\base\Model;

/**
 * Class BulkEditForm
 *
 * This model is for modifying multiple orders
 *
 * @package frontend\models\forms
 */
class ReopenOrderEditForm extends ScheduledOrder
{

    public string $open_date ;
    public bool $reopen_enable = false;

    public bool $confirmed = false;


    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['confirmed', 'reopen_enable'], 'boolean'],
            [['open_date'], 'string'],
            [['open_date'], 'required', 'when' => function($model){
                //district required if country is set
                return $model->reopen_enable === true;
            }, 'whenClient' => "function (attribute, value){
                return $('#reopenordereditform-reopen_enable').val() != true;
            }"],
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
//        $this->orders = preg_split($pattern, $this->order_ids);

        return parent::beforeValidate();
    }
}
