<?php

namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_history".
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $created_date
 * @property string $notes
 */
class BaseOrderHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['created_date'], 'safe'],
            [['notes'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'order_id'     => 'Order ID',
            'created_date' => 'Created Date',
            'notes'      => 'Notes',
        ];
    }
}
