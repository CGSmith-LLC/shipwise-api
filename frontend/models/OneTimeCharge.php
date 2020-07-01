<?php


namespace frontend\models;


class OneTimeCharge extends \common\models\OneTimeCharge
{

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $this->amount = $this->setFromDecimalAmount();
        return parent::beforeSave($insert);
    }
}