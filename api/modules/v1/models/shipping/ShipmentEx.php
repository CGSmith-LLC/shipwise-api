<?php

namespace api\modules\v1\models\shipping;

use common\models\shipping\Shipment;

/**
 * Class ShipmentEx
 *
 * @package api\modules\v1\models\shipping
 */
class ShipmentEx extends Shipment
{

    /**
     * Calculated rates
     *
     * @var ShipmentRateEx[]
     */
    private array $_rates = [];

    /**
     * Override parent to return array of ShipmentRateEx objects
     * @return ShipmentRateEx[]
     */
    public function getRates()
    {
        foreach (parent::getRates() as $rate) {
            $this->_rates[] = new ShipmentRateEx($rate);
        }

        return $this->_rates;
    }
}
