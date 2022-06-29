<?php

namespace common\models\base;

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
class BaseItem extends \yii\db\ActiveRecord
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
            [['order_id', 'quantity', 'alias_quantity'], 'integer'],
            [['sku', 'uuid', 'alias_sku', 'type'], 'string', 'max' => 64],
            [['notes'], 'string', 'max' => 512],
            ['name', 'string', 'max' => 128],
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