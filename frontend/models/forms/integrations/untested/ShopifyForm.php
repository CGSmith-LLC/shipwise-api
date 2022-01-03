<?php

namespace frontend\models\forms\integrations;

use frontend\models\forms\BaseIntegrationsForm;

class ShopifyForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'Shopify';

    public $baseUri = '';
    public $apiKey = '';
    public $apiSecret = '';
    public $locationID = '';


    public function rules()
    {
        return [
            [['baseUri', 'apiKey','apiSecret', 'locationID'], 'required'],
            [['baseUri', 'apiKey','apiSecret', 'locationID'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'baseUri' => 'Shopify Store URL',
            'apiKey' => 'API Key',
            'apiSecret' => 'API Secret',
            'locationID' => 'Location ID',
        ];
    }
}