<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class EcommerceIntegrationQuery
 * @package common\models\query
 */
class EcommerceIntegrationQuery extends ActiveQuery
{
    public function for(?int $userId = null, ?int $customerId = null): EcommerceIntegrationQuery
    {
        if ($userId) {
            $this->andWhere(['user_id' => $userId]);
        }

        if ($customerId) {
            $this->andWhere(['customer_id' => $customerId]);
        }

        return $this;
    }

    public function orderById(int $sort = SORT_ASC): EcommerceIntegrationQuery
    {
        return $this
            ->orderBy(['id' => $sort]);
    }
}
