<?php

namespace frontend\models;

use common\models\Order as BaseOrder;
use yii\helpers\ArrayHelper;

/**
 * Class Order
 *
 * @package frontend\models
 */
class Order extends BaseOrder
{

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $return = parent::rules();
        $return[] = [['requested_ship_date'], 'date', 'format' => 'php:m/d/Y'];

        return $return;
    }


    /** {@inheritdoc} */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id'           => 'ShipWise ID',
            'status_id'    => 'Status',
            'status.name'  => 'Status',
            'address'      => 'Ship To',
            'carrier_id'   => 'Carrier',
            'service_id'   => 'Service',
            'tracking'     => 'Tracking Number',
            'notes'        => 'Order Notes',
            'customer_id'  => 'Customer',
            'carrier.name' => 'Carrier',
            'service.name' => 'Service',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        if (!empty($this->requested_ship_date)) {
            $date = new \DateTime($this->requested_ship_date);
            $this->setAttribute('requested_ship_date', $date->format('m/d/Y'));
        }

        parent::afterFind();
    }

    /**
     * Delete all related entities.
     *
     * @return boolean
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function deleteRelatedEntities()
    {
        $result = true;

        // Address
        if ($this->address) {
            $result = $result && $this->address->delete();
        }

        // Items
        foreach ($this->items as $item) {
            $result = $result && $item->delete();
        }

        return $result;
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Normalize datetime input
        if (!empty($this->requested_ship_date)) {
            $date = new \DateTime($this->requested_ship_date);
            $this->requested_ship_date = $date->format('Y-m-d H:i:s');
        }

        return true;
    }

    /**
     * Changes order status
     *
     * @param int $newStatusId
     *
     * @return bool Whether the saving succeeded
     */
    public function changeStatus($newStatusId)
    {
        $this->status_id = $newStatusId;
        $this->updated_date = date("Y-m-d H:i:s");

        return $this->save();
    }
}
