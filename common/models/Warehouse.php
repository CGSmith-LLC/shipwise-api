<?php

namespace common\models;

use common\models\query\WarehouseQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Whether the customer is linked to given user
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isLinkedToUser($userId)
    {
        return $this->getUserWarehouse()->onCondition(['user_id' => (int)$userId])->exists();
    }

    public static function find()
    {
        return new WarehouseQuery(get_called_class());
    }

    /**
     * Returns list of customers as array [id=>name]
     *
     * @param string $keyField   Field name to use as key
     * @param string $valueField Field name to use as value
     *
     * @todo getList is used in multiple spots and might be worthwhile to make an interface
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name')
    {
        $data = self::find()->orderBy([$valueField => SORT_ASC])->all();

        return ArrayHelper::map($data, $keyField, $valueField);
    }

    /**
     * Get UserCustomer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserWarehouse()
    {
        return $this->hasMany(UserWarehouse::class, ['warehouse_id' => 'id']);
    }
}
