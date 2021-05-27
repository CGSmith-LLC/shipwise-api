<?php

namespace frontend\models\forms;

use frontend\models\Customer;
use Yii;
use yii\base\Model;

/**
 * Class ReportForm
 *
 * @package frontend\models\forms
 *
 * @property string $start_date
 * @property string $end_date
 * @property int    $customer
 */
class ReportForm extends Model
{

    public $start_date;
    public $end_date;
    public $customer;

    public function rules()
    {
        return [
            [['start_date', 'end_date', 'customer'], 'required'],
            [
                'customer',
                'in',
                'range' => array_keys(
                    Yii::$app->user->identity->isAdmin
                        ? Customer::getList()
                        : Yii::$app->user->identity->getCustomerList()
                ),
            ],
        ];
    }


}
