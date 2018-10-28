<?php

namespace common\models\base;

/**
 * This is the model class for table "api_consumer".
 *
 * @property int    $id
 * @property string $auth_key            API consumer key. Used for authentication
 * @property string $auth_secret         API consumer secret. Used for authentication
 * @property string $auth_token          The API token obtained during authentication
 * @property string $last_activity       Last user activity date-time
 * @property int    $customer_id         Customer ID
 * @property int    $status              API consumer status. 1:active, 0:inactive
 * @property string $created_date        API consumer creation date
 */
class BaseApiConsumer extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'api_consumer';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['auth_key', 'auth_secret'], 'required'],
			[['last_activity', 'created_date'], 'safe'],
			[['customer_id', 'status', 'superuser'], 'integer'],
			[['auth_key'], 'string', 'max' => 6],
			[['auth_secret', 'auth_token'], 'string', 'max' => 32],
			[['auth_key'], 'unique'],
			[['auth_secret'], 'unique'],
		];
	}
}