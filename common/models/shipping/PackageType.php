<?php

namespace common\models\shipping;

/**
 * Class PackageType
 *
 * @package common\models\shipping
 */
class PackageType
{

    const MY_PACKAGE = 'MyPackage';
    const ENVELOPE   = 'Envelope';
    const PAK        = 'Pak';

    private static $list = [
        self::MY_PACKAGE => self::MY_PACKAGE,
        self::ENVELOPE   => self::ENVELOPE,
        self::PAK        => self::PAK,
    ];

    public static function exists($type)
    {
        return defined('self::_' . $type);
    }

    /**
     * Map carrier package codes to ShipWise codes.
     *
     * @return array
     */
    public static function mapper()
    {
        return [
            // FedEx - array key is FedEx package code, array value is corresponding ShipWise package code.
            Carrier::FEDEX => [
                'YOUR_PACKAGING' => self::MY_PACKAGE,
                'FEDEX_ENVELOPE' => self::ENVELOPE,
                'FEDEX_PAK'      => self::PAK,
            ],
            // UPS - array key is UPS package code, array value is corresponding ShipWise package code.
            Carrier::UPS   => [
                '02' => self::MY_PACKAGE,
                '01' => self::ENVELOPE,
                '04' => self::PAK,
            ],
        ];
    }

    /**
     * @param string $carrier
     * @param null   $shipwiseCode
     * @param null   $carrierCode
     *
     * @return bool|int|mixed|string
     */
    public static function map($carrier, $shipwiseCode = null, $carrierCode = null)
    {
        $mapper = self::mapper();

        // Get carrier code.
        if (isset($shipwiseCode)) {
            foreach ($mapper[$carrier] as $key => $value) {
                if ($value == $shipwiseCode) {
                    return $key;
                }
            }
        } // Get ShipWise code.
        elseif (isset($carrierCode)) {
            if (isset($mapper[$carrier][$carrierCode])) {
                return $mapper[$carrier][$carrierCode];
            }
        }
        return false;
    }

    /**
     * Returns list of package types as array [code=>name]
     *
     * @return array
     */
    public static function getList()
    {
        return static::$list;
    }
}