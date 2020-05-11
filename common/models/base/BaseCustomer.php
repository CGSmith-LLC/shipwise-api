<?php

namespace common\models\base;

use common\models\PaymentMethod;

/**
 * This is the model class for table "customers".
 *
 * @property int                  $id
 * @property string               $name
 * @property string               $address1 Address line 1
 * @property string               $address2 Address line 2
 * @property string               $city     City
 * @property int                  $state_id State ID
 * @property string               $zip      ZIP code
 * @property string               $phone    Phone number
 * @property string               $email    Email address
 * @property string               $logo     The absolute URL of the logo
 * @property string               $stripe_customer_id   Stripe ID for the customer
 * @property string               $created_date
 *
 * @property \common\models\State $state
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
            [['address1', 'city', 'state_id', 'zip', 'logo'], 'required'],
            [['state_id'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['address1', 'address2', 'city'], 'string', 'max' => 64],
            [['zip'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 32],
            [['stripe_customer_id'], 'string', 'max' => 128],
            [['logo', 'email'], 'string', 'max' => 255],
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
            'address1'     => 'Address1',
            'address2'     => 'Address2',
            'city'         => 'City',
            'state_id'     => 'State ID',
            'zip'          => 'Zip',
            'phone'        => 'Phone',
            'email'        => 'Email',
            'logo'         => 'Logo',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Get State
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne('common\models\State', ['id' => 'state_id']);
    }

    /**
     * Payment method relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, ['customer_id' => 'id']);
    }
}
