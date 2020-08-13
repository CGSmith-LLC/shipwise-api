<?php


namespace frontend\models;


class OneTimeCharge extends \common\models\OneTimeCharge
{

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        $this->setAttribute('amount', $this->setFromDecimalAmount());
        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->decimalAmount = $this->getDecimalAmount();
        parent::afterFind();
    }
}