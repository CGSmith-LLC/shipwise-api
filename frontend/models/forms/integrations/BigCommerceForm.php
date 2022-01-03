<?php

namespace frontend\models\forms\integrations;

use frontend\models\forms\BaseIntegrationsForm;

/** @property string $dropDownName */
class BigCommerceForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'BigCommerce';

    public $storeHash;
    public $accessToken;
    public $clientId;
    public $clientSecret;
    public $statusId;


    public function rules()
    {
        return [
            [['storeHash', 'accessToken', 'clientId', 'clientSecret', 'statusId'], 'required'],
            [['storeHash', 'accessToken', 'clientId', 'clientSecret'], 'string'],
            [['statusId'], 'integer'],
            [['statusId'], 'default', 'value' => '11'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'storeHash' => 'Store Hash',
            'accessToken' => 'Access Token',
            'clientId' => 'Client ID',
            'clientSecret' => 'Client Secret',
            'statusId' => 'Status ID pull orders in on',
        ];
    }

}