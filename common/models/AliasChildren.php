<?php

namespace common\models;

use common\models\query\AliasChildrenQuery;

/**
 * This is the model class for table "alias_children".
 *
 * @property int $id
 * @property int $alias_id
 * @property string $sku
 * @property int $name
 * @property int $quantity
 */
class AliasChildren extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alias_children';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alias_id', 'sku', 'name', 'quantity'], 'required'],
            [['alias_id', 'quantity'], 'integer'],
            [['sku', 'name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias_id' => 'Alias ID',
            'sku' => 'SKU',
            'name' => 'Name',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * {@inheritdoc}
     * @return AliasChildrenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AliasChildrenQuery(get_called_class());
    }
}
