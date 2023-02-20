<?php

namespace frontend\models\forms;

use common\models\ScheduledOrder;

/**
 * Class ReopenOrderEditForm
 *
 * This model is for modifying reopen orders scheduler
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
                return $model->reopen_enable === true;
            }, 'whenClient' => "function (attribute, value){
                return $('#reopenordereditform-reopen_enable').val() != true;
            }"],
        ];
    }

    public function attributeLabels()
    {
        return [
            'open_date' => 'Open date'
        ];
    }
}
