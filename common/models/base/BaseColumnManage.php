<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "column_manage".
 *
 * @property int $id
 * @property int $user_id Reference to user table
 * @property string $column_data
 */
class BaseColumnManage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'column_manage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return ['id' => 'ID', 'user_id' => 'User ID', 'column_data' => 'Column Json Data',];
    }
}
