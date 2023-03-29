<?php

namespace common\models;

use common\models\base\BaseState;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\traits\CacheableListTrait;

/**
 * Class State
 *
 * @package common\models
 */
class State extends BaseState
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'states-list';
    final public const DEFAULT_COUNTRY_ABBR = 'US';

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
    public function getCountry()
    {
        return $this->hasOne('common\models\Country', ['id' => 'abbreviation']);
    }

    /**
     * Get array of State ids
     *
     * @return array
     */
    public static function getIdsAsArray()
    {
        $array = ArrayHelper::getColumn(self::find()->select('id')->asArray()->cache(86400)->all(), 'id');
        $array[] = 0;
        return $array;
    }

    /**
     * For given country code find state by either abbreviation or name
     *
     * @param string $country
     * @param string $abbr State abbreviation
     * @param string $name State name
     *
     * @return State|ActiveRecord|bool The State model or False if not found
     */
    public static function findByAbbrOrName($country, $abbr = '', $name = ''): \common\models\State|\yii\db\ActiveRecord|bool
    {
        if (empty($country) || (empty($abbr) && empty($name))) {
            return false;
        }

        // Match data in DB
        $country = strtoupper($country);
        $abbr = strtoupper($abbr);

        if (($state = self::find()->where(['country' => $country, 'abbreviation' => $abbr])->one()) !== null) {
            return $state;
        }

        if (($state = self::find()->where(['country' => $country, 'name' => $name])->one()) !== null) {
            return $state;
        }

        return false;
    }
}
