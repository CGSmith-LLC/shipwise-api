<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "bulk_action".
 *
 * @property int                       $id
 * @property string                    $code       Code of the bulk action
 * @property string                    $name       Name of the bulk action
 * @property int                       $status     Current status. 0:processing, 1:completed, 2:error
 * @property int                       $print_mode Printing mode. 1: qz plugin, 2: pdf file
 * @property string                    $created_on Created timestamp
 * @property int                       $created_by ID of the user who created/triggered bulk action
 *
 * @property \frontend\models\User     $createdBy
 * @property \common\models\BulkItem[] $items
 */
class BaseBulkAction extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bulk_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['status', 'print_mode', 'created_by'], 'integer'],
            [['created_on'], 'safe'],
            [['code'], 'string', 'max' => 60],
            [['name'], 'string', 'max' => 120],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'code'               => 'Code',
            'name'               => 'Name',
            'status'             => 'Status',
            'print_mode'         => 'Print Mode',
            'created_on'         => 'Created On',
            'created_by'         => 'Created By',
            'createdBy.username' => 'Created By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BulkActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BulkActionQuery(get_called_class());
    }

    /**
     * Get Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne('frontend\models\User', ['id' => 'created_by']);
    }

    /**
     * Get bulk items (orders)
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('common\models\BulkItem', ['bulk_action_id' => 'id']);
    }
}
