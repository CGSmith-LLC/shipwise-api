<?php

namespace common\models\base;

/**
 * This is the model class for table "customers".
 *
 * @property int    $id
 * @property string $name
 * @property string $created_date
 */
class BaseCustomer extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'customers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'created_date'], 'safe'],
			[['name'], 'string', 'max' => 45],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'           => 'ID',
			'name'         => 'Name',
			'created_date' => 'Created Date',
		];
	}
}