<?php

namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
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
            [['user_id', 'order_id'], 'required'],
            [['user_id', 'order_id'], 'integer'],
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
            'id' => 'ID',
            'user_id' => 'User',
            'order_id' => 'Order',
            'created_date' => 'Created Date',
            'notes' => 'Notes',
        ];
    }
}
