<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "behavior".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $integration_id
 * @property string $name
 * @property string $description
 * @property string $event
 * @property int $status
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 */
class Behavior extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'behavior';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'integration_id', 'behavior', 'name', 'event', 'status', 'order'], 'required'],
            [['customer_id', 'integration_id', 'status', 'order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'event'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'integration_id' => Yii::t('app', 'Integration ID'),
            'behavior' => Yii::t('app', 'Behavior'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'event' => Yii::t('app', 'Event'),
            'status' => Yii::t('app', 'Status'),
            'order' => Yii::t('app', 'Order'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
