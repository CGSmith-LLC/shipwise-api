<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\Subscription;

class SubscriptionQuery extends ActiveQuery
{
    public function isActive(): SubscriptionQuery
    {
        return $this->andOnCondition(['is_active' => Subscription::IS_TRUE]);
    }

    public function isNotActive(): SubscriptionQuery
    {
        return $this->andOnCondition(['is_active' => Subscription::IS_FALSE]);
    }

    public function isTrial(): SubscriptionQuery
    {
        return $this->andOnCondition(['is_trial' => Subscription::IS_TRUE]);
    }

    public function isNotTrial(): SubscriptionQuery
    {
        return $this->andOnCondition(['is_trial' => Subscription::IS_FALSE]);
    }

    public function isNotSynced(): SubscriptionQuery
    {
        return $this->andOnCondition("unsync_usage_quantity > 0");
    }
}
