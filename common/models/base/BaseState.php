<?php

namespace common\models\base;

/**
 * This is the model class for table "states".
 *
 * @property int    $id
 * @property string $name
 * @property string $abbreviation
 */
class BaseState extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'states';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'abbreviation'], 'required'],
			[['name'], 'string', 'max' => 45],
			[['abbreviation'], 'string', 'max' => 12],
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
			'abbreviation' => 'Abbreviation',
		];
	}
}