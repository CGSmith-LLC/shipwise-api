<?php

namespace api\modules\v1\models\core;

use common\models\shipping\Service;

/**
 * Class ServiceEx
 *
 * @package api\modules\v1\models\core
 */
class ServiceEx extends Service
{

    /**
     * @SWG\Definition(
     *     definition = "Service",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "Service ID" ),
     *     @SWG\Property( property = "name", type = "string", description = "Service name" ),
     *     @SWG\Property( property = "carrier", ref = "#/definitions/Carrier" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'name', 'carrier'];
    }


    public static function getListForCsvBox($carrierId)
    {
        $services = self::getList(carrierId: $carrierId);
        $newArray = [];

        foreach ($services as $id => $name) {
            $newArray[] = ['value' => (string) $id, 'display_label' => $name];
        }
        return $newArray;
    }

    /**
     * @param int|string|null $carrier Carrier ID or shipwise_code. Optional.
     *
     * @return array
     */
    public static function getShipwiseCodes($carrier = null)
    {
        $carriers = CarrierEx::getShipwiseCodes();
        $flipped  = array_flip($carriers);

        if (is_numeric($carrier) && isset($carriers[$carrier])) {
            $carrierId = $carrier;
        } elseif (in_array($carrier, array_values($carriers))) {
            $carrierId = $flipped[$carrier];
        } else {
            $carrierId = $flipped;
        }

        return self::getList('id', 'shipwise_code', $carrierId);
    }
}