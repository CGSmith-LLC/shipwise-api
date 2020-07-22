<?php

namespace common\models;

use common\models\base\BaseState;
use yii\helpers\ArrayHelper;

/**
 * Class State
 *
 * @package common\models
 */
class State extends BaseState
{

    /**
     * Get array of State ids
     *
     * @return array
     */
    public static function getIdsAsArray()
    {
        $array = ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
        $array[] = 0;
        return $array;
    }

    /**
     * Returns list of states as array [id=>name]
     *
     * @param string $keyField   Field name to use as key
     * @param string $valueField Field name to use as value
     *
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name')
    {
        $data = self::find()->orderBy([$valueField => SORT_ASC])->all();

        return ArrayHelper::map($data, $keyField, $valueField);
    }
}