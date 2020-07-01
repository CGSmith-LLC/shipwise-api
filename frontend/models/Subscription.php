<?php


namespace frontend\models;

use common\models\Subscription as BaseSubscription;

/**
 * Class Subscription
 *
 * @property SubscriptionItems[]  $items
 *
 * @package frontend\models
 */
class Subscription extends BaseSubscription
{

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        if (!empty($this->next_invoice)) {
            $date = new \DateTime($this->next_invoice);
            $this->setAttribute('next_invoice', $date->format('m/d/Y'));
        }

        parent::afterFind();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (!empty($this->next_invoice)) {
            $date = new \DateTime($this->next_invoice);
            $this->setAttribute('next_invoice', $date->format('Y-m-d'));
        }

        return parent::beforeSave($insert);
    }
}