<?php

namespace common\models;

use common\models\base\BaseOrder;
use common\models\query\OrderQuery;

/**
 * Class Order
 *
 * @package common\models
 *
 * @property Customer $customer
 * @property Address $address
 * @property TrackingInfo $trackingInfo
 * @property Item[] $items
 * @property Status $status
 * @property OrderHistory[] $history
 */
class Order extends BaseOrder
{
    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }

    /**
     * Get Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

    /**
     * Get Ship To Address
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne('common\models\Address', ['id' => 'address_id']);
    }

    /**
     * Get Tracking Info
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrackingInfo()
    {
        return $this->hasOne('common\models\TrackingInfo', ['id' => 'tracking_id']);
    }

    /**
     * Get Order Items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('common\models\Item', ['order_id' => 'id']);
    }

    /**
     * Get Order Status
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne('common\models\Status', ['id' => 'status_id']);
    }

    /**
     * Get Order History
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany('common\models\OrderHistory', ['order_id' => 'id']);
    }
}