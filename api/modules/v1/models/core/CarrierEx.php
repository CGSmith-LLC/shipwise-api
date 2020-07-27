<?php

namespace api\modules\v1\models\core;

use common\models\shipping\Carrier;

/**
 * Class CarrierEx
 *
 * @package api\modules\v1\models\core
 */
class CarrierEx extends Carrier
{

    /**
     * Override parent to define only those carriers that are implemented in API.
     * @see Carrier::$shipwiseCodes for all available.
     *
     * @var array
     */
    protected static $shipwiseCodes = [
        self::FEDEX => 'FedEx',
        self::UPS   => 'UPS',
    ];

    /**
     * @SWG\Definition(
     *     definition = "Carrier",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "Carrier ID" ),
     *     @SWG\Property( property = "name", type = "string", description = "Carrier name" ),
     * )
     */

    /** {@inheritdoc} */
    public function fields()
    {
        return ['id', 'name'];
    }
}