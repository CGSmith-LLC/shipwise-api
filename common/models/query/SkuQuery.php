<?php

namespace common\models\query;


use common\models\Sku;

/**
 * This is the ActiveQuery class for [[Sku]].
 *
 * @see Sku
 */
class SkuQuery extends BaseQuery
{
    /**
     * Query condition to get order by order id
     *
     * @param int $id Order Id
     *
     * @return SkuQuery
     */
    public function byId($id)
    {
        return $this->andWhere([Sku::tableName() . '.id' => (int)$id]);
    }

    /**
     * Query condition to get order by SKU
     *
     * @param string $sku SKU number
     *
     * @return SkuQuery
     */
    public function bySku($sku)
    {
        return $this->andWhere([Sku::tableName() . '.sku' => (string)$sku]);
    }
}
