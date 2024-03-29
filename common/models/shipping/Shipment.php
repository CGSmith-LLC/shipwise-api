<?php

namespace common\models\shipping;

use common\models\base\BaseShipment;
use common\models\State;
use Exception;
use LogicException;
use Yii;
use yii\db\Expression;

/**
 * Class Shipment
 *
 * @property Carrier           $carrier
 * @property Service           $service
 * @property ShipmentPackage[] $packages
 * @property string            $mergedLabelsData
 * @property string            $mergedLabelsFormat
 * @property string            $masterTrackingNum
 *
 * @package common\models\shipping
 */
class Shipment extends BaseShipment
{

    const WEIGHT_UNITS_LB = 'LB';
    const WEIGHT_UNITS_KG = 'KG';
    const DIM_UNITS_IN    = 'IN';
    const DIM_UNITS_CM    = 'CM';

    /**
     * Carrier
     *
     * @var Carrier
     */
    public $carrier = null;

    /**
     * Service
     *
     * @var Service
     */
    public $service = null;

    /**
     * Contains all merged package labels file data in base64
     *
     * @var string
     */
    public $mergedLabelsData = '';

    /**
     * File format of the merged package labels data in base64. eg: PDF
     *
     * @var string
     */
    public $mergedLabelsFormat = '';

    /**
     * Master tracking number
     *
     * @var string
     */
    protected $masterTrackingNum;

    /**
     * Calculated rates
     *
     * @var ShipmentRate[]
     */
    private $_rates = [];

    /**
     * Packages
     *
     * @var ShipmentPackage[]
     */
    protected $_packages = [];

    /**
     * Package Type
     *
     * @var PackageType
     */
    protected $_packageType = null;

    /**
     * Carrier extension plugin
     *
     * @var ShipmentPlugin|null
     */
    protected $plugin = null;

    const ADDRESS_TYPE_BUSINESS    = 'business';
    const ADDRESS_TYPE_RESIDENTIAL = 'residential';

    /** @var array */
    protected static $addressTypes = [
        self::ADDRESS_TYPE_BUSINESS    => self::ADDRESS_TYPE_BUSINESS,
        self::ADDRESS_TYPE_RESIDENTIAL => self::ADDRESS_TYPE_RESIDENTIAL,
    ];

    /** @return array */
    public static function getAddressTypes()
    {
        return static::$addressTypes;
    }

    public function getRates()
    {
        return $this->_rates;
    }

    public function setRates($value)
    {
        $this->_rates = (array)$value;
    }

    /**
     * Append rate object to rates array
     *
     * @param ShipmentRate $rate
     */
    public function addRate(ShipmentRate $rate)
    {
        $this->_rates[] = $rate;
    }

    /**
     * Add package to shipment
     *
     * @param ShipmentPackage $package
     */
    public function addPackage(ShipmentPackage $package)
    {
        $this->_packages[] = $package;
    }

    /**
     * Get shipment packages
     *
     * @return array|ShipmentPackage[]
     */
    public function getPackages()
    {
        return $this->_packages;
    }

    public function setPackages($packages)
    {
        $this->_packages = $packages;
    }

    /**
     * Get package type
     *
     * @return null|PackageType
     */
    public function getPackageType()
    {
        return $this->_packageType;
    }

    public function setPackageType(PackageType $type)
    {
        $this->_packageType = $type;
    }

    /**
     * Set Plugin
     *
     * Set a ShipmentPlugin to be used in getting rates and create shipments
     *
     * @param ShipmentPlugin $plugin Shipment plugin to make use of
     */
    protected function setPlugin(ShipmentPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Get warnings from shipping plugin
     *
     * @return array|bool
     */
    public function getPluginWarnings()
    {
        if ($this->plugin) {
            return $this->plugin->getWarnings();
        } else {
            return false;
        }
    }

    /**
     * Get Shipment Total Weight
     *
     * Calculate shipment total weight based on shipment packages
     *
     * @return float Total weight
     */
    public function getTotalWeight()
    {
        $result   = 0;
        $packages = $this->getPackages();
        foreach ($packages as $package) {
            $result += $package->weight;
        }
        return $result;
    }

    /**
     * Count shipment packages
     *
     * @param bool $includeQtyItems
     *
     * @return int Total packages in shipment
     */
    public function countPackages($includeQtyItems = false)
    {
        $result   = 0;
        $packages = $this->getPackages();
        if (is_a($packages, '\yii\db\ActiveQuery')) {
            $packages = $packages->all();
        }
        foreach ($packages as $package) {
            $result += ($includeQtyItems ? $package->quantity : 1);
        }
        return $result;
    }

    /**
     * Get Shipment Total Dimensional Weight
     *
     * Calculate shipment total cubic (dimensional) weight based on shipment packages
     *
     * @return float Total weight
     */
    public function getTotalDimensionalWeight()
    {
        $result   = 0;
        $packages = $this->getPackages();

        $divisor = ($this->weight_units == 'KG') ? 6000 : 166;

        foreach ($packages as $package) {
            $dimWeight = ($package->length * $package->width * $package->height * $package->quantity) / $divisor;
            $result    += $dimWeight;
        }
        return $result;
    }

    /**
     * Get Rates from carrier API
     *
     * This method will submit the request using ShipmentPlugin to shipping carrier to get rates
     * it will then return an array with service name, estimated delivery date and detailed rate
     *
     * @return Shipment|string Error
     * @throws LogicException
     * @throws ShipmentException
     * @version 2019.12.20
     *
     */
    public function rate()
    {
        if ($this->plugin === null) {
            $this->initCarrierPlugin();
        }

        if (!$this->plugin) {
            throw new LogicException("Shipment has no ShipmentPlugin");
        }

        if (count($this->getPackages()) < 1) {
            throw new LogicException("Shipment has no packages");
        }

        try {
            $this->plugin->rate($this);
            return $this;
        } catch (Exception $e) {
            throw new ShipmentException(
                $e->getMessage() . " - in ShipmentPlugin::{$this->plugin->getPluginName()}::rate()"
            );
        }
    }

    /**
     * Convert datetime to UTC
     *
     * @param string $datetime Datetime to convert
     *
     * @return string UTC datetime
     * @throws Exception
     */
    public function convertDateToUTC($datetime)
    {
        $date = new \DateTime($datetime, new \DateTimeZone(Yii::$app->formatter->timeZone));
        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date->format("Y-m-d H:i:s");
    }

    /**
     * Get Plugin Warnings
     *
     * Returns list of warning from the plugins. These are more like notices
     * than errors. They are not fatal. Fatal errors will throw Exceptions. You
     * might want to log these and investigate though.
     *
     * @return bool|array Array of warnings, grouped by plugin or false if none
     */
    public function getWarnings()
    {
        $warnings = false;
        if ($this->plugin && ($pluginWarnings = $this->plugin->getWarnings()) !== false) {
            $warnings[$this->plugin->getPluginName()] = $pluginWarnings;
        }

        return $warnings;
    }

    /**
     * Initialize Carrier Plugin
     *
     * Based on shipment carrier ID initialize corresponding shipment plugin extension
     * and autoload its settings
     */
    protected function initCarrierPlugin()
    {
        $carrierCode = $this->carrier->name ?? null;
        $carrierCode = str_replace(" ", "", $carrierCode);

        $class = "\\common\\models\\shipping\\extension\\{$carrierCode}Plugin";

        if (!($carrierCode && class_exists($class))) {
            $this->addError('plugin', "Shipping carrier plugin not found");
            return false;
        }

        $plugin = new $class();
        $plugin->autoload($this->customer_id);

        $this->setPlugin($plugin);
    }

    /**
     * This function will transform US state or Canada province full-name to abbreviation if necessary.
     *
     * @param string $country Country ISO code
     * @param string $state   State or Province
     *
     * @return string Teh abbreviation code, eg. IL. Or the $state param ASIS if not found.
     */
    public static function recognizeState($country, $state)
    {
        if (in_array($country, ['US', 'CA'])) {
            $input = strtoupper(trim($state));
            if (strlen($input) == 2) {
                return $input;
            } else {
                $name = new Expression("UPPER(name)");
                if (($_state = State::find()->where(['=', $name, $input])->one()) !== null) {
                    return $_state->abbreviation;
                }
            }
        }

        return $state;
    }

    /**
     * Ship the shipment using carrier API
     *
     * This method will submit a shipment creation request to carrier API
     * to get the tracking number and label.
     *
     * @return Shipment|string Error
     * @throws LogicException
     * @throws ShipmentException
     * @version 2020.02.25
     *
     *
     */
    public function ship()
    {
        if ($this->plugin === null) {
            $this->initCarrierPlugin();
        }

        if (!$this->plugin) {
            throw new LogicException("Shipment has no ShipmentPlugin");
        }

        if (count($this->getPackages()) < 1) {
            throw new LogicException("Shipment has no packages");
        }

        try {
            $this->plugin->ship($this);
            return $this;
        } catch (Exception $e) {
            Yii::error($e);
            throw new ShipmentException(
                $e->getMessage() . " - in ShipmentPlugin::{$this->plugin->getPluginName()}::ship()"
            );
        }
    }

    /**
     * Get shipment master tracking number
     *
     * @return bool|string
     */
    public function getMasterTracking()
    {
        if (!empty($this->masterTrackingNum)) {
            return $this->masterTrackingNum;
        }

        $firstPackage = $this->packages[0] ?? null;

        if (!$firstPackage) {
            return false;
        }

        return $firstPackage->master_tracking_num;
    }

    /**
     * Set shipment master tracking number
     *
     * @param string $number
     */
    public function setMasterTrackingNumber($number)
    {
        $this->masterTrackingNum = $number;
    }

    /**
     * Set merged labels data
     *
     * @param string $data Base64 data
     */
    public function setMergedLabelsData($data)
    {
        $this->mergedLabelsData = $data;
    }

    /**
     * Set merged labels data format
     *
     * @param string $format File format of the merged labels data in base64. eg: PDF
     */
    public function setMergedLabelsFormat($format)
    {
        $this->mergedLabelsFormat = $format;
    }
}
