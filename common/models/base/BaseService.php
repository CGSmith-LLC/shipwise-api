<?php

namespace common\models\base;

/**
 * This is the model class for table "service".
 *
 * @property int    $id
 * @property string $name
 * @property int    $carrier_id
 * @property string $shipwise_code ShipWise service code
 * @property string $carrier_code  Service code name as used by carrier's API
 */
class BaseService extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'carrier_id', 'shipwise_code', 'carrier_code'], 'required'],
            [['carrier_id'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['shipwise_code', 'carrier_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => 'Name',
            'carrier_id'    => 'Carrier ID',
            'shipwise_code' => 'Shipwise Code',
            'carrier_code'  => 'Carrier Code',
        ];
    }
}