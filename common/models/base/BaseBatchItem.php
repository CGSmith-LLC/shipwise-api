<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "batch_item".
 *
 * @property int $id
 * @property int $batch_id
 * @property int $order_id
 */
class BaseBatchItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'batch_id'], 'required'],
            [['order_id', 'batch_id'], 'integer'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne('common\models\Order', ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne('common\models\base\BaseBatch', ['id' => 'base_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
}
