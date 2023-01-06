<?php

namespace frontend\models\forms;

use frontend\models\Customer;
use Yii;
use yii\base\Model;

/**
 * Class SpeedeeManifestForm
 *
 * @package frontend\models\forms
 *
 * @property int $customer
 */
class SpeedeeManifestForm extends Model
{
    public $customer;

    public function rules()
    {
        return [
            [['customer'], 'required'],
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