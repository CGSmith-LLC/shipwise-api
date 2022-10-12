<?php

namespace common\models;

use common\models\query\AliasParentQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "alias_parent".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $alias
 * @property string $name
 * @property int $active
 */
class AliasParent extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alias_parent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'sku', 'name'], 'required'],
            [['customer_id', 'active'], 'integer'],
            [['sku'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
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
            'sku' => 'SKU',
            'name' => 'Name',
            'active' => 'Active',
        ];
    }

    /**
     * Get Alias Items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(AliasChildren::class, ['alias_id' => 'id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return AliasParentQuery the active query used by this AR class.
     */
    public static function find()
    {
        // If user is not admin, then show items that ONLY belong to current user
        if (\Yii::$app->id !== 'app-api' &&  !\Yii::$app->user->identity->isAdmin) {
            $query = new AliasParentQuery(get_called_class(), ['tableName' => AliasParent::tableName()]);
            $query->forCustomers(\Yii::$app->user->identity->customerIds);
            return $query;
        }
        return new AliasParentQuery(get_called_class());
    }
}
