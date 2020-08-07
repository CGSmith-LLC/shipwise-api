<?php

namespace common\models;

use common\models\base\BaseState;
use common\models\shipping\Service;
use yii\helpers\ArrayHelper;

/**
 * Class State
 *
 * @package common\models
 */
class State extends BaseState
{

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
     * Returns list of states as array [id=>name]
     *
     * @param string $keyField Field name to use as key
     * @param string $valueField Field name to use as value
     * @param string|array|null $country Country or array of Countries. Optional.
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name', $country = null)
    {
        $query = self::find();
        if ($country) {
            $query->andWhere([State::tableName() . '.country' => $country]);
            $query->andWhere(['IN', State::tableName() . '.country', $country]);
        }
        $query->orderBy([$keyField => SORT_ASC, $valueField => SORT_ASC]);

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}