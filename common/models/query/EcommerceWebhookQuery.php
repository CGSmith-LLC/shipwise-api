<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\EcommerceWebhook;

/**
 * Class EcommerceWebhookQuery
 * @package common\models\query
 */
class EcommerceWebhookQuery extends ActiveQuery
{
    public function received(): EcommerceWebhookQuery
    {
        return $this->andWhere(['status' => EcommerceWebhook::STATUS_RECEIVED]);
    }

    public function forPlatformId(int $platformId): EcommerceWebhookQuery
    {
        $this->andWhere(['platform_id' => $platformId]);
        return $this;
    }

    public function orderById(int $sort = SORT_ASC): EcommerceWebhookQuery
    {
        return $this->orderBy(['id' => $sort]);
    }
}
