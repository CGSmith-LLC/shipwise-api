<?php

namespace common\models;

use common\models\query\SkuQuery;

/**
 * This is the model class for table "sku".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $sku
 * @property string $name
 * @property string $substitute_1
 * @property string $substitute_2
 * @property string $substitute_3
 * @property boolean $excluded
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
            [['name', 'substitute_1', 'substitute_2', 'substitute_3'], 'string', 'max' => 64],
            [['customer_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @return SkuQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SkuQuery(get_called_class(), ['tableName' => Sku::tableName()]);
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
