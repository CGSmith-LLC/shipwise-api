<?php

namespace common\models\base;

/**
 * This is the model class for table "addresses".
 *
 * @property int    $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property int    $state_id
 * @property string $zip
 * @property string $phone
 * @property string $notes
 * @property string $created_date
 * @property string $updated_date
 */
class BaseAddress extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'addresses';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'address1', 'city', 'state_id', 'zip', 'phone'], 'required'],
            [['email'], 'email'],
			[['state_id'], 'integer'],
			[['created_date', 'updated_date'], 'safe'],
			[['name', 'address1', 'address2', 'city'], 'string', 'max' => 64],
			[['zip'], 'string', 'max' => 16],
			[['phone'], 'string', 'max' => 32],
			[['notes'], 'string', 'max' => 140],
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
			'email'        => 'Email',
			'address1'     => 'Address1',
			'address2'     => 'Address2',
			'city'         => 'City',
			'state_id'     => 'State ID',
			'zip'          => 'Zip',
			'phone'        => 'Phone',
			'notes'        => 'Notes',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		];
	}
}