<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\EcommercePlatform;

/**
 * Class EcommercePlatformQuery
 * @package common\models\query
 */
class EcommercePlatformQuery extends ActiveQuery
{
    public function active(): EcommercePlatformQuery
    {
        return $this
            ->andWhere(['status' => EcommercePlatform::STATUS_PLATFORM_ACTIVE]);
    }

    public function for(?int $userId = null, ?int $customerId = null): EcommercePlatformQuery
    {
        $this->joinWith([
            'ecommerceIntegration' => function ($query) use ($userId, $customerId) {
                if ($userId) {
                    $query->onCondition(['ecommerce_integration.user_id' => $userId]);
                }

                if ($customerId) {
                    $query->onCondition(['ecommerce_integration.customer_id' => $customerId]);
                }
            }
        ]);

        return $this;
    }

    public function orderById(int $sort = SORT_ASC): EcommercePlatformQuery
    {
        return $this
            ->orderBy(['id' => $sort]);
    }
}
