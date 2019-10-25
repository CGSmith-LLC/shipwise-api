<?php

namespace frontend\models;

use common\models\Address as BaseAddress;
use yii\helpers\ArrayHelper;

/**
 * Class Address
 *
 * @package frontend\models
 */
class Address extends BaseAddress
{
    /** {@inheritdoc} */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'address1' => 'Address Line 1',
            'address2' => 'Address Line 2',
            'state_id' => 'State',
            'notes'    => 'Address Notes',
        ]);
    }

}