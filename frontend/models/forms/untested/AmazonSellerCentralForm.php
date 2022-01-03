<?php

namespace frontend\models\forms\integrations;

use common\models\forms\BaseForm;
use frontend\models\forms\BaseIntegrationsForm;

class AmazonSellerCentralForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'Amazon Seller Central';

    public $sellerID = '';
    public $marketplaceID = '';
    public $mwsAuthToken = '';


    public function rules()
    {
        return [
            [['sellerID', 'marketplaceID','mwsAuthToken'], 'required'],
            [['sellerID', 'marketplaceID','mwsAuthToken'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sellerID' => 'Seller ID',
            'marketplaceID' => 'Marketplace ID',
            'mwsAuthToken' => 'MWS Auth Token',
        ];
    }
}