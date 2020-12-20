<?php

namespace common\models\base;

/**
 * This is the model class for table "package_items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $package_id
 * @property int $quantity
 * @property string $sku
 * @property string $uuid
 * @property string $name
 */
class BasePackageItem extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'package_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'package_id', 'quantity', 'sku'], 'required'],
            [['order_id', 'package_id', 'quantity'], 'integer'],
            [['sku', 'uuid'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
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
        ];
    }
}