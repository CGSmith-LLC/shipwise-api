<?php

namespace common\models\base;

use yii\db\{ActiveRecord, ActiveQuery};
use common\models\{EcommercePlatform, Customer};
use common\models\query\EcommerceIntegrationQuery;
use frontend\models\User;

/**
 * This is the model class for table "ecommerce_integration".
 *
 * @property int $id
 * @property int $user_id
 * @property int $customer_id
 * @property int $platform_id
 * @property int $status
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 *
 * @property Customer $customer
 * @property EcommercePlatform $platform
 * @property User $user
 */
class BaseEcommerceIntegration extends ActiveRecord
{
    /**
     * @return EcommerceIntegrationQuery
     */
    public static function find(): EcommerceIntegrationQuery
    {
        return new EcommerceIntegrationQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'ecommerce_integration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['meta'], 'default', 'value' => null],
            [['user_id', 'customer_id', 'platform_id'], 'required'],
            [['user_id', 'customer_id', 'platform_id', 'status'], 'integer'],
            [['meta'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['platform_id'], 'exist', 'skipOnError' => true, 'targetClass' => EcommercePlatform::class, 'targetAttribute' => ['platform_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'customer_id' => 'Customer ID',
            'platform_id' => 'Platform ID',
            'status' => 'Status',
            'meta' => 'Meta Data',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEcommercePlatform(): ActiveQuery
    {
        return $this->hasOne(EcommercePlatform::class, ['id' => 'platform_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
