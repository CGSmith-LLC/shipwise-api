<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "bulk_item".
 *
 * @property int                    $id
 * @property int                    $bulk_action_id Ref to Bulk Action
 * @property int                    $order_id       Ref to Order
 * @property string                 $queue_id       Queue message ID if any
 * @property int                    $status         Current status. 0:queued, 1:done, 2:error
 *
 * @property \common\models\Order[] $orders
 */
class BaseBulkItem extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bulk_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bulk_action_id'], 'required'],
            [['bulk_action_id', 'order_id', 'status'], 'integer'],
            [['queue_id'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'bulk_action_id' => 'Bulk Action ID',
            'order_id'       => 'Order ID',
            'queue_id'       => 'Queue ID',
            'status'         => 'Status',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BulkItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BulkItemQuery(get_called_class());
    }

    /**
     * Get orders
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany('common\models\Order', ['order_id' => 'id']);
    }
}
