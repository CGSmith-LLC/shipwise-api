<?php

namespace common\models\base;

/**
 * This is the model class for table "items".
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $quantity
 * @property string $sku
 * @property string $name
 */
class BaseItem extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'items';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['order_id', 'quantity', 'sku'], 'required'],
			[['order_id', 'quantity'], 'integer'],
			[['sku', 'name'], 'string', 'max' => 64],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'       => 'ID',
			'order_id' => 'Order ID',
			'quantity' => 'Quantity',
			'sku'      => 'Sku',
			'name'     => 'Name',
		];
	}
}