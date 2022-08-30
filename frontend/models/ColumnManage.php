<?php

namespace frontend\models;

use common\models\base\BaseColumnManage;
use yii\helpers\{Html, Json, Url};
use Yii;

/**
 * This is the model class for table "column_manage".
 *
 * @property int $id
 * @property int $user_id Reference to user table
 * @property string $column_data
 */
class ColumnManage extends BaseColumnManage {
    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getColumnManageOfUser() {
        $user_id = Yii::$app->user->id;

        if (!$user_id) {
            return [];
        }

        $columnData = ColumnManage::find()->where(['user_id' => $user_id])->one();
        if (empty($columnData)) {
            $customColumns = new ColumnManage();
            $customColumns->user_id = $user_id;
            $defaultColumns = array();
            $columns = array('customer_reference', 'po_number', 'carrier_id', 'service_id', 'address', 'tracking', 'created_date', 'requested_ship_date', 'notes', 'status_id',);
            foreach($columns as $column) {
                $defaultColumns[] = ['attribute' => $column, 'status' => 1];
            }

            $customColumns->column_data = json_encode($defaultColumns);
            $customColumns->save();
            return $customColumns;
        } else {
            return $columnData;
        }
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function generateColumns() {
        $customColumns = self::getColumnManageOfUser();
        foreach(json_decode($customColumns->column_data) as $column) {
            if ($column->status == 1) {
                $generateColumns[] = $column->attribute;
            }
        }
        return $generateColumns;
    }
}
