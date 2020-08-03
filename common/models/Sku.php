<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sku".
 *
 * @property int $id
 * @property string $sku
 * @property string $name
 */
class Sku extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sku';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sku'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
            [['customer_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Sku',
            'name' => 'Item Name',
            'customer_id' => 'Customer',
        ];
    }
    /**
     * Get Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }


}
