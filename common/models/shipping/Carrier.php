<?php

namespace common\models\shipping;

use common\traits\CacheableListTrait;
use common\models\base\BaseCarrier;
use yii\helpers\ArrayHelper;

/**
 * Class Carrier
 *
 * @package common\models\shipping
 */
class Carrier extends BaseCarrier
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'carriers-list';

    /* Please keep synchronized with database IDs */
    const FEDEX        = 1;
    const UPS          = 2;
    const USPS         = 3;
    const DHL          = 4;
    const AMAZON_UPS   = 5;
    const AMAZON_FEDEX = 6;
    const AMAZON_USPS  = 11;

    /** @var array */
    private static $shipwiseCodes = [
        self::FEDEX        => 'FedEx',
        self::UPS          => 'UPS',
        self::USPS         => 'USPS',
        self::DHL          => 'DHL',
        self::AMAZON_UPS   => 'AMAZON_UPS',
        self::AMAZON_FEDEX => 'AMAZON_FEDEX',
        self::AMAZON_USPS  => 'AMAZON_USPS',
    ];

    const REPRINT_BEHAVIOUR_CREATE_NEW = 1;
    const REPRINT_BEHAVIOUR_EXISTING   = 2;

    /**
     * Carriers with re-print behaviour of REPRINT_BEHAVIOUR_EXISTING.
     *
     * The re-print behaviour of reprinting existing was implemented for carriers that require purchasing a label.
     * Unlike other carrier APIs such as FedEx or UPS, Amazon MWS API performs a purchasing of a label, so we don't want
     * to re-purchase a new label each time we re-print.
     *
     * @var int[]
     */
    private static $reprintExisting = [
        self::AMAZON_UPS,
        self::AMAZON_FEDEX,
        self::AMAZON_USPS,
    ];

    public function init(): void
    {
        $this->setClearCacheEvents();
        parent::init();
    }

    /**
     * Shipping label re-print behaviour.
     *
     * - REPRINT_BEHAVIOUR_CREATE_NEW Makes API call to create new label
     * - REPRINT_BEHAVIOUR_EXISTING   Retrieves previously created label
     *
     * @return int
     */
    public function getReprintBehaviour()
    {
        return in_array(
            $this->id,
            self::$reprintExisting
        ) ? self::REPRINT_BEHAVIOUR_EXISTING : self::REPRINT_BEHAVIOUR_CREATE_NEW;
    }

    /** @return array */
    public static function getShipwiseCodes()
    {
        return static::$shipwiseCodes;
    }

    /**
     * Find carrier by ShipWise code
     *
     * @param string $code ShipWise code. See self::$shipwiseCodes
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findByShipWiseCode($code)
    {
        if (empty($code)) {
            return null;
        }

        $flipped = array_flip(self::$shipwiseCodes);

        return self::find()->where(['id' => $flipped[$code]])->one();
    }

    /**
     * Find carrier by service ShipWise code
     *
     * @param string $serviceShipwiseCode Service ShipWise code.
     *
     * @return Carrier|\yii\db\ActiveRecord|null
     */
    public static function findByServiceCode($serviceShipwiseCode)
    {
        if (empty($serviceShipwiseCode)) {
            return null;
        }

        if (($service = Service::findByShipWiseCode($serviceShipwiseCode)) !== null) {
            return self::find()->where(['id' => $service->carrier_id])->one();
        }

        return null;
    }
}
