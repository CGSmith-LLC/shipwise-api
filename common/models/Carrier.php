<?php

namespace common\models;

use common\models\base\BaseCarrier;
use yii\helpers\ArrayHelper;

/**
 * Class Carrier
 *
 * @package common\models
 */
class Carrier extends BaseCarrier
{

    /**
     * Returns list of carriers as array [id=>name]
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