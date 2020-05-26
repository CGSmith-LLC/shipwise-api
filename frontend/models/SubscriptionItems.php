<?php


namespace frontend\models;


use common\models\SubscriptionItems as BaseSubscriptionItems;

/**
 * Class SubscriptionItems
 *
 * @package frontend\models
 */
class SubscriptionItems extends BaseSubscriptionItems
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