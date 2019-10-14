<?php

namespace common\models;

use common\models\base\BaseStatus;
use yii\helpers\ArrayHelper;

/**
 * Class Status
 *
 * @package common\models
 */
class Status extends BaseStatus
{
    /**
     * Get array of Status ids
     *
     * @return array
     */
    public static function getIdsAsArray()
    {
        return ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
    }

    /**
     * Returns list of statuses as array [id=>name]
     *
     * @param string $keyField   Field name to use as key
     * @param string $valueField Field name to use as value
     *
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name')
    {
        $query = self::find()->orderBy(['name' => SORT_ASC]);

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }
}