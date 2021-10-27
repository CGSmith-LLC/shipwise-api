<?php

namespace frontend\models\forms\integrations;


use frontend\models\forms\BaseIntegrationsForm;

/** @property string $dropDownName */
class WooCommerceForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'WooCommerce';

    public $url;
    public $apiKey;
    public $apiPassword;
    public $orderStatus;


    public function rules()
    {
        return [
            [['url', 'apiKey','apiPassword', 'orderStatus'], 'required'],
            [['url', 'apiKey','apiPassword', 'orderStatus'], 'string'],
            [['orderStatus'], 'default', 'value' => 'processing'],
        ];
    }

    public function attributeLabels()
    {
        return [
          'url' => 'Store URL',
          'apiKey' => 'API Key',
          'apiPassword' => 'API Password',
          'orderStatus' => 'Status to pull orders in on',
        ];
    }

}