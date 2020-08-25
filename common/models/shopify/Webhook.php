<?php

namespace common\models\shopify;

use Yii;

/**
 * This is the model class for table "shopify_webhook".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $shopify_webhook_id
 * @property string $created_date
 */
class Webhook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shopify_webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'shopify_webhook_id'], 'required'],
            [['customer_id'], 'integer'],
            [['created_date'], 'safe'],
            [['shopify_webhook_id'], 'string', 'max' => 64],
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
            'shopify_webhook_id' => 'Shopify Webhook ID',
            'created_date' => 'Created Date',
        ];
    }
}
