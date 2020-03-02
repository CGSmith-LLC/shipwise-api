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
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $result = $this->deleteRelatedEntities();

            return $result;
        } else {
            return false;
        }
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

    /**
     * Changes order status
     *
     * @param int $newStatusId
     *
     * @return bool Whether the saving succeeded
     */
    public function changeStatus($newStatusId)
    {
        $this->status_id    = $newStatusId;
        $this->updated_date = date("Y-m-d H:i:s");

        return $this->save();
    }
}
