<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;

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

    public static function getList(string $keyField = 'id', string $valueField = 'name'): array
    {
        if ($keyField == 'id' && $valueField == 'name') { // We cache only default values to avoid multi-storing
            $data = \Yii::$app->cache->get(self::LIST_CACHE_KEY);

            if (!$data) {
                $data = self::find()->orderBy([$valueField => SORT_ASC])->all();
                $data = ArrayHelper::map($data, $keyField, $valueField);
                \Yii::$app->cache->set(self::LIST_CACHE_KEY, $data, 30 * 86400); // 30 days
            }
        } else {
            $data = self::find()->orderBy([$valueField => SORT_ASC])->all();
            $data = ArrayHelper::map($data, $keyField, $valueField);
        }

        return $data;
    }
}
