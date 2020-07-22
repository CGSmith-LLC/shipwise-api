<?php


namespace frontend\models\forms;


use yii\base\Model;

class DashboardForm extends Model
{

    public $start_date;
    public $end_date;
    public $customers;


    public function rules()
    {
        return [
            [['start_date', 'end_date', 'customers'], 'required'],
        ];
    }

}