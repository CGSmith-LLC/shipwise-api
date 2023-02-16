<?php

namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "items".
 *
 * @property int    $id
 * @property string $uuid
 * @property int    $order_id
 * @property int    $quantity
 * @property string $sku
 * @property int    $alias_quantity
 * @property string $alias_sku
 * @property string $name
 * @property string $notes
 */
class BaseItem extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'quantity', 'sku'], 'required'],
            [['order_id', 'alias_quantity'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
            [['sku', 'uuid', 'alias_sku', 'type'], 'string', 'max' => 64],
            [['notes', 'name'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'order_id' => 'Order ID',
            'quantity' => 'Quantity',
            'sku'      => 'Sku',
            'name'     => 'Name',
            'notes'    => 'Notes',
        ];
    }
}