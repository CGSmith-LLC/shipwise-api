<?php

namespace common\models\shopify;

use Yii;

/**
 * This is the model class for table "shopify_app".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $shop
 * @property string $scopes
 * @property string $access_token
 * @property string $created_date
 */
class Shopify extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shopify_app';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'shop', 'scopes'], 'required'],
            [['customer_id'], 'integer'],
            [['created_date'], 'safe'],
            [['shop', 'scopes', 'access_token'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'shop' => 'Shop',
            'scopes' => 'Scopes',
            'access_token' => 'Access Token',
            'created_date' => 'Created Date',
        ];
    }
}
