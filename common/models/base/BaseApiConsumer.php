<?php

namespace common\models\base;


use common\models\query\ApiConsumerQuery;

/**
 * This is the model class for table "api_consumer".
 *
 * @property int $id
 * @property string $auth_key            API consumer key. Used for authentication
 * @property string $auth_secret         API consumer secret. Used for authentication
 * @property string $auth_token          The API token obtained during authentication
 * @property string $last_activity       Last user activity date-time
 * @property int $customer_id         Customer ID
 * @property int $status              API consumer status. 1:active, 0:inactive
 * @property string $created_date        API consumer creation date
 * @property string $encrypted_secret   Encrypted API secret key
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
            [['id'], 'integer'],
            [['auth_key', 'encrypted_secret'], 'required'],
            [['last_activity', 'created_date'], 'safe'],
            [['customer_id', 'status', 'superuser'], 'integer'],
            [['auth_key'], 'string', 'max' => 6],
            [['auth_key'], 'unique'],
            [['label'], 'string', 'max' => 128],
            [['encrypted_secret'], 'string'],
            [['encrypted_secret'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_key' => 'Auth Key',
            'encypted_secret' => 'Auth Secret',
            'customer_id' => 'Customer ID',
            'label' => 'Label',
        ];
    }


    /**
     * @inheritdoc
     * @return ApiConsumerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ApiConsumerQuery(get_called_class());
    }
}