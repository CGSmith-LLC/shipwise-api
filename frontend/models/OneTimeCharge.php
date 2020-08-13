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


    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        /** @var OneTimeCharge $item */
        foreach ($provider as $item) {
            if (!$item->added_to_invoice) {
                $total += $item->$fieldName;
            }
        }
        return $total / 100;
    }
}