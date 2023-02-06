<?php

namespace common\traits;

use common\models\State;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\shipping\Service;

trait CacheableListTrait
{
    protected function setClearCacheEvents(): void
    {
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'clearCache']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'clearCache']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'clearCache']);
    }

    protected function clearCache($event): void
    {
        Yii::$app->cache->delete(self::LIST_CACHE_KEY);
    }

    public static function getList(?string $keyField = null, ?string $valueField = null, mixed $additionalField = null): array
    {
        $defaultKeyField = match (__CLASS__) {
            'common\models\Status',
            'common\models\shipping\Carrier',
            'common\models\shipping\Service',
            'common\models\Customer',
            'frontend\models\Customer',
            'common\models\State' => 'id',
            'common\models\Country' => 'abbreviation',
            default => null,
        };

        $defaultValueField = match (__CLASS__) {
            'common\models\Status',
            'common\models\shipping\Carrier',
            'common\models\shipping\Service',
            'common\models\Customer',
            'frontend\models\Customer',
            'common\models\State',
            'common\models\Country' => 'name',
            default => null,
        };

        // We cache only default values to avoid multi-storing
        $isCacheable = (is_null($keyField) && is_null($valueField) && !$additionalField);
        $data = null;

        if ($isCacheable) {
            $data = Yii::$app->cache->get(self::LIST_CACHE_KEY);
        }

        if (!$data) {
            $data = match (__CLASS__) {
                'common\models\shipping\Service' => self::getServicesData($defaultKeyField, $defaultValueField, $additionalField),
                'common\models\State' => self::getStatesData($defaultKeyField, $defaultValueField, $additionalField),
                default => self::getStandardData($defaultKeyField, $defaultValueField),
            };

            if ($isCacheable) {
                Yii::$app->cache->set(self::LIST_CACHE_KEY, $data, 30 * 86400); // 30 days
            }
        }

        return $data;
    }

    protected static function getStandardData(string $keyField, string $valueField): array
    {
        $data = self::find()->orderBy([$valueField => SORT_ASC])->all();
        return ArrayHelper::map($data, $keyField, $valueField);
    }

    protected static function getServicesData(string $keyField, string $valueField, mixed $additionalField): array
    {
        $query = self::find();

        if ($additionalField) {
            $query->andWhere([Service::tableName() . '.carrier_id' => $additionalField]);
            $query->andWhere(['IN', Service::tableName() . '.carrier_id', $additionalField]);
        }

        $query->orderBy([$keyField => SORT_ASC, $valueField => SORT_ASC]);
        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    protected static function getStatesData(string $keyField, string $valueField, mixed $additionalField): array
    {
        $query = self::find();

        if ($additionalField) {
            $query->andWhere([State::tableName() . '.country' => $additionalField]);
            $query->andWhere(['IN', State::tableName() . '.country', $additionalField]);
        }

        $query->orderBy([$keyField => SORT_ASC, $valueField => SORT_ASC]);
        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}
