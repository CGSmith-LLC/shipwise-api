<?php

namespace frontend\models;

use common\models\base\BaseColumnManage;
use Yii;

/**
 * This is the model class for table "column_manage".
 *
 * @property int $id
 * @property int $user_id Reference to user table
 * @property string $column_data
 */
class ColumnManage extends BaseColumnManage
{
    /**
     * @param $user_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getColumnManageOfUser() {
        $user_id = Yii::$app->user->id;

        if (!$user_id) {
            return [];
        }

        $columnData = ColumnManage::find()->where(['user_id' => $user_id])->one();
        if(empty($columnData)) {
            $customColumns = new ColumnManage();
            $customColumns->user_id = $user_id;
            $defaultColumns = array(
                [
                    'attribute' => 'customer_reference',
                    'status' => 1,
                ],
                [
                    'attribute' => 'po_number',
                    'status' => 1,
                ],
                [
                    'attribute' => 'address',
                    'status' => 1,
                ],
                [
                    'attribute' => 'tracking',
                    'status' => 1,
                ],
                [
                    'attribute' => 'requested_ship_date',
                    'status' => 1,
                ],
                [
                    'attribute' => 'notes',
                    'status' => 1,
                ],
                [
                    'attribute' => 'created_at',
                    'status' => 1,
                ],
                [
                    'attribute' => 'status_id',
                    'status' => 1,
                ],
            );

            $customColumns->column_data = json_encode($defaultColumns);
            $customColumns->save();
            return $customColumns;
        } else {
            return $columnData;
        }
    }
}
