<?php

namespace common\models\base;

/**
 * This is the model class for table "customers".
 *
 * @property int    $id
 * @property string $customer_id
 * @property string $key
 * @property string $value
 * @property string $created_date
 */
class BaseCustomerMeta extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers_meta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_date'], 'safe'],
            [['key', 'value'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'customer_id'  => 'Customer ID',
            'key'          => 'Key',
            'value'        => 'Value',
            'created_date' => 'Created Date',
        ];
    }
}