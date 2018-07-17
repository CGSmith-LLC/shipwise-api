<?php

namespace common\models\base;

/**
 * This is the model class for table "carrier".
 *
 * @property int    $id
 * @property string $name
 */
class BaseCarrier extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'carrier';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 45],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'   => 'ID',
			'name' => 'Name',
		];
	}
}