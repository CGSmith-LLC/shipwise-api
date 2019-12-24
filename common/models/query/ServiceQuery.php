<?php

namespace common\models\query;

use common\models\shipping\Service;

/**
 * This is the ActiveQuery class for [[Service]].
 *
 * @see Service
 */
class ServiceQuery extends \yii\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\shipping\Service[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Service|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    /**
     * Add condition to find by carrier id and carrier's service code
     *
     * @param int    $carrierId          Carrier ID
     * @param string $carrierServiceCode Service code as used by carrier API
     *
     * @return $this
     */
    public function forCarrierService($carrierId, $carrierServiceCode)
    {
        return $this->andWhere([
            Service::tableName() . '.carrier_id'   => $carrierId,
            Service::tableName() . '.carrier_code' => $carrierServiceCode,
        ]);
    }

    /**
     * Find service by ShipWise code
     *
     * @param string $code Service ShipWise code.
     *
     * @return $this
     */
    public function byShipWiseCode($code)
    {
        return $this->andWhere([Service::tableName() . '.shipwise_code' => $code]);
    }

    /**
     * Query condition to get order by order id
     *
     * @param int $id Order Id
     *
     * @return OrderQuery
     */
    public function byId($id)
    {
        return $this->andWhere([Order::tableName() . '.id' => (int)$id]);
    }
}
