<?php

namespace common\models\base;

/**
 * This is the model class for table "status".
 *
 * @property int    $id
 * @property string $name
 */
class BaseStatus extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'status';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
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