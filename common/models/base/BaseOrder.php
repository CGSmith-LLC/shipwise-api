<?php

namespace common\models\base;

/**
 * This is the model class for table "orders".
 *
 * @property int    $id
 * @property int    $customer_id
 * @property string $order_reference
 * @property string $customer_reference
 * @property int    $status_id
 * @property string $tracking
 * @property string $created_date
 * @property string $updated_date
 * @property int    $address_id
 * @property string $notes
 */
class BaseOrder extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'orders';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['customer_id', 'customer_reference', 'address_id'], 'required'],
			[['customer_id', 'status_id', 'address_id'], 'integer'],
			[['created_date', 'updated_date'], 'safe'],
			[['order_reference', 'tracking'], 'string', 'max' => 45],
			[['customer_reference'], 'string', 'max' => 64],
			[['notes'], 'string', 'max' => 140],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'                 => 'ID',
			'customer_id'        => 'Customer ID',
			'order_reference'    => 'Order Reference',
			'customer_reference' => 'Customer Reference',
			'status_id'          => 'Status ID',
			'tracking'           => 'Tracking',
			'created_date'       => 'Created Date',
			'updated_date'       => 'Updated Date',
			'address_id'         => 'Address ID',
			'notes'              => 'Notes',
		];
	}
}