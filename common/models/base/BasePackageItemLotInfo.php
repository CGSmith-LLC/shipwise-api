<?php

namespace common\models\base;

/**
 * This is the model class for table "package_items_lot_info".
 *
 * @property int $id
 * @property int $package_items_id
 * @property int $quantity
 * @property string $lot_number
 * @property string $serial_number
 * @property string $created_date
 */
class BasePackageItemLotInfo extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'package_items_lot_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['package_items_id', 'quantity'], 'required'],
            [['package_items_id'], 'integer'],
            [['lot_number', 'serial_number'], 'string', 'max' => 128],
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