<?php

namespace frontend\models\forms\integrations;

use frontend\models\forms\BaseIntegrationsForm;

/** @property string $dropDownName */
class WebhookForm extends BaseIntegrationsForm
{
    public static string $dropDownName = 'Webhook';

    public $adapter;


    public function rules()
    {
        return [
            [['adapter'], 'required'],
            [['adapter'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'adapter' => 'Adapter to transform data received by webhook',
        ];
    }

}