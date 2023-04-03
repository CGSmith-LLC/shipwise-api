<?php

namespace common\models\shipping;

use common\models\base\BaseShipmentPackage;

/**
 * Class ShipmentPackage
 *
 * @package common\models\shipping
 */
class ShipmentPackage extends BaseShipmentPackage
{

    // Package contents
    final const CONTENTS_DOCUMENTS     = 'DOCUMENTS';
    final const CONTENTS_NON_DOCUMENTS = 'NON_DOCUMENTS';

    private static array $contentsList = [
        self::CONTENTS_DOCUMENTS     => 'Documents',
        self::CONTENTS_NON_DOCUMENTS => 'Parcel',
    ];

    /**
     * Returns array of package contents types
     *
     * @return array
     */
    public static function getContentsList()
    {
        return self::$contentsList;
    }

    /**
     * Get package cubic volume
     *
     * @param int $divisor Volume divisor. ie. 1000 for cubic metres
     *
     * @return float Volume
     */
    public function getCubicVolume($divisor = 1000)
    {
        return (($this->length * $this->width * $this->height * $this->quantity) / $divisor);
    }
}
