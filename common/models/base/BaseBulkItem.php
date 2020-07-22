<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "bulk_item".
 *
 * @property int                       $id
 * @property int                       $bulk_action_id  Ref to Bulk Action
 * @property int                       $order_id        Ref to Order
 * @property string                    $job             Job name
 * @property string                    $queue_id        Queue message ID if any
 * @property string                    $base64_filedata File encoded in base64
 * @property string                    $base64_filetype Type of encoded file: PDF, PNG.
 * @property int                       $status          Current status. 0:queued, 1:done, 2:error
 * @property string                    $errors          Processing error messages encoded in JSON
 *
 * @property \common\models\BulkAction $bulkAction
 * @property \common\models\Order      $order
 */
class BaseBulkItem extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bulk_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bulk_action_id'], 'required'],
            [['bulk_action_id', 'order_id', 'status'], 'integer'],
            [['base64_filedata', 'errors'], 'string'],
            [['job'], 'string', 'max' => 255],
            [['queue_id'], 'string', 'max' => 60],
            [['base64_filetype'], 'string', 'max' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'bulk_action_id'  => 'Bulk Action ID',
            'order_id'        => 'Order ID',
            'job'             => 'Job',
            'queue_id'        => 'Queue ID',
            'base64_filedata' => 'Base64 File Data',
            'base64_filetype' => 'Base64 File Type',
            'errors'          => 'Errors',
            'status'          => 'Job status',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BulkItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BulkItemQuery(get_called_class());
    }

    /**
     * Get Bulk Action
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBulkAction()
    {
        return $this->hasOne('common\models\BulkAction', ['id' => 'bulk_action_id']);
    }

    /**
     * Get order
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne('common\models\Order', ['id' => 'order_id']);
    }
}
