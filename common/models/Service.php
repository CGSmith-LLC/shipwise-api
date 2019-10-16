<?php

namespace common\models;

use common\models\base\BaseService;
use yii\helpers\ArrayHelper;

/**
 * Class Service
 *
 * @package common\models
 */
class Service extends BaseService
{
    /**
     * Get Carrier
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrier()
    {
        return $this->hasOne('common\models\Carrier', ['id' => 'carrier_id']);
    }

    /**
     * Get array of Service ids
     *
     * @return array
     */
    public static function getIdsAsArray()
    {
        return ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
    }

    /**
     * Returns services array
     *
     * Optionally pass the carrier id to get services for specific carrier.
     *
     * @param string   $keyField   Field name to use as key
     * @param string   $valueField Field name to use as value
     * @param int|null $carrierId  Carrier ID. Optional.
     *
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name', $carrierId = null)
    {
        $query = self::find();
        if ($carrierId) {
            $query->andWhere([Service::tableName() . '.carrier_id' => $carrierId]);
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}