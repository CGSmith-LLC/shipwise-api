<?php

namespace common\models\base;

/**
 * This is the model class for table "packages".
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $tracking
 * @property string $length
 * @property string $width
 * @property string $height
 * @property string $weight
 * @property string $created_date
 */
class BasePackage extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'tracking'], 'required'],
            [['order_id'], 'integer'],
            ['tracking', 'string', 'max' => 64],
            [['length', 'width', 'height', 'weight'], 'string', 'max' => 16],
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
            'tracking' => 'Tracking Number',
        ];
    }
}