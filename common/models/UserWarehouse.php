<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_warehouse".
 *
 * @property int $id
 * @property int $warehouse_id
 * @property int $user_id
 */
class UserWarehouse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'user_id'], 'required'],
            [['warehouse_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Warehouse ID',
            'user_id' => 'User ID',
        ];
    }
}
