<?php

namespace api\modules\v1\models\core;

use common\models\Country;

/**
 * Class StateEx
 *
 * @package api\modules\v1\models\core
 */
class CountryEx extends Country
{

    /**
     * @SWG\Definition(
     *     definition = "Country",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "State ID" ),
     *     @SWG\Property( property = "name", type = "string", description = "State name" ),
     *     @SWG\Property( property = "abbreviation", type = "string",  description = "State abbreviation" ),
     *     @SWG\Property( property = "country", type = "string",  description = "Country the state or province originated from" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'name', 'abbreviation'];
    }


    public static function getListForCsvBox($id = null)
    {
        $list = self::getList(keyField: 'abbreviation', valueField: 'abbreviation');
        $newArray = [];
        \Yii::debug($list);

        foreach ($list as $id => $abbreviation) {
            $newArray[] = ['value' => (string) $id, 'display_label' => $abbreviation, 'dependents' => StateEx::getListForCsvBox($id)];
        }

        return $newArray;
    }
}