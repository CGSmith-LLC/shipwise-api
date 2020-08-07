<?php

namespace common\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $name
 * @property string $created_date
 */
class BaseBatch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'customer_id'], 'required'],
            [['customer_id'], 'integer'],
            [['created_date'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchItems()
    {
        return $this->hasMany('common\models\base\BaseBatchItem', ['batch_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_date' => 'Created Date',
        ];
    }


    /**
     * Returns list of Batches as array [abbreviation=>name]
     *
     * @param string $keyField Field name to use as key
     * @param string $valueField Field name to use as value
     * @param null $sortField what field to sort on
     * @param int $sortDirection SORT_ASC by default
     * @param array $where
     *
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name', $sortField = null, $sortDirection = SORT_ASC, $where = [])
    {
        $query = self::find();

        if (!is_null($sortField)) {
            $query->orderBy([$sortField => $sortDirection]);
        }

        if (!empty($where)) {
            $query->where($where);
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}
