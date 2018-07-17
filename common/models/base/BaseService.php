<?php

namespace common\models\base;

/**
 * This is the model class for table "service".
 *
 * @property int    $id
 * @property string $name
 * @property int    $carrier_id
 */
class BaseService extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'service';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'carrier_id'], 'required'],
			[['carrier_id'], 'integer'],
			[['name'], 'string', 'max' => 45],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'         => 'ID',
			'name'       => 'Name',
			'carrier_id' => 'Carrier ID',
		];
	}
}