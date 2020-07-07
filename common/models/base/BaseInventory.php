<?php

namespace common\models\base;


/**
 *  This is the model class for table "inventory".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string $sku
 * @property float $available_quantity
 */
class BaseInventory extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['customer_id','sku'], 'required'],
            [['name','sku'], 'string', 'max' => 64],
            [['available_quantity'], 'number'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'customer_id' => 'Customer ID',
            'name' => 'Name',
            'sku' => 'Sku',
            'available_quantity' => 'Available Quantity',
        ];
    }

    /**
     * Get Available quantity
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

}