<?php

namespace frontend\models\forms\integrations;

use frontend\models\forms\BaseIntegrationsForm;

/** @property string $dropDownName */
class AmericoldForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'Americold';
    public string $integration = 'fulfillment';

    public $customerId;

    public function rules()
    {
        return [
            [['customerId'], 'required'],
            [['customerId'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customerId' => '3PL Customer ID',
        ];
    }

}