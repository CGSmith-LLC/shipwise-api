<?php

namespace common\models\base;

/**
 * This is the model class for table "order_history".
 *
 * @property int    $id
 * @property int    $status_id
 * @property int    $order_id
 * @property string $created_date
 * @property string $comment
 */
class BaseOrderHistory extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'order_history';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['status_id', 'order_id'], 'required'],
			[['status_id', 'order_id'], 'integer'],
			[['created_date'], 'safe'],
			[['comment'], 'string'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'           => 'ID',
			'status_id'    => 'Status ID',
			'order_id'     => 'Order ID',
			'created_date' => 'Created Date',
			'comment'      => 'Comment',
		];
	}
}