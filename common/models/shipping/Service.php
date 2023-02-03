<?php

namespace common\models\shipping;

use common\models\base\BaseService;
use common\models\query\ServiceQuery;
use yii\helpers\ArrayHelper;
use common\traits\CacheableListTrait;

/**
 * Class Service
 *
 * @property Carrier $carrier
 *
 * @package common\models\shipping
 */
class Service extends BaseService
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'services-list';

    public function init(): void
    {
        $this->setClearCacheEvents();
        parent::init();
    }

    /**
     * Get Carrier
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrier()
    {
        return $this->hasOne('common\models\shipping\Carrier', ['id' => 'carrier_id']);
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
     * @param string $keyField   Field name to use as key
     * @param string $valueField Field name to use as value
     * @param int|array|null $carrierId  Carrier ID or array of IDs. Optional.
     *
     * @return array
     */
    public static function getList(string $keyField = 'id', string $valueField = 'name', int|array $carrierId = null): array
    {
        if ($keyField == 'id' && $valueField == 'name' && $carrierId == null) { // We cache only default values to avoid multi-storing
            $data = \Yii::$app->cache->get(self::LIST_CACHE_KEY);

            if (!$data) {
                $query = self::find();
                $query->orderBy([$keyField => SORT_ASC, $valueField => SORT_ASC]);
                $data = ArrayHelper::map($query->all(), $keyField, $valueField);

                \Yii::$app->cache->set(self::LIST_CACHE_KEY, $data, 30 * 86400); // 30 days
            }
        } else {
            $query = self::find();

            if ($carrierId) {
                $query->andWhere([Service::tableName() . '.carrier_id' => $carrierId]);
                $query->andWhere(['IN', Service::tableName() . '.carrier_id', $carrierId]);
            }

            $query->orderBy([$keyField => SORT_ASC, $valueField => SORT_ASC]);

            $data = ArrayHelper::map($query->all(), $keyField, $valueField);
        }

        return $data;
    }

    /**
     * @param int|null $carrierId Carrier ID or shipwise_code. Optional.
     *
     * @return array
     */
    public static function getShipwiseCodes($carrierId = null)
    {
        return self::getList('id', 'shipwise_code', $carrierId);
    }

    /**
     * Find service by ShipWise code
     *
     * @param string $code Service ShipWise code.
     *
     * @return array|Service|null
     */
    public static function findByShipWiseCode($code)
    {
        if (empty($code)) {
            return null;
        }

        return self::find()->byShipWiseCode($code)->one();
    }

    /**
     * @inheritdoc
     * @return ServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServiceQuery(get_called_class());
    }
}
