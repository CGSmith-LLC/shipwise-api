<?php

namespace common\models\shipping;

use Yii;

/**
 * Class ShipmentPlugin
 *
 * @property Shipment $shipment
 *
 * @package common\models\shipping
 */
abstract class ShipmentPlugin extends ShipmentConnection
{

    /**
     * Run Calls as Production
     *
     * @var bool
     */
    protected $isProduction;

    /**
     * Shipment Object
     *
     * @var Shipment
     */
    protected $shipment;

    /**
     * Call Data
     *
     * @var mixed
     */
    protected $data;

    /**
     * Response Data
     *
     * @var mixed
     */
    protected $response;

    /**
     * Base Tracking URL
     *
     * @var string
     */
    protected $trackingURL;

    /**
     * Whether the shipment was processed through carrier API
     *
     * @var bool
     */
    protected $isShipped = false;

    /**
     * Warnings
     *
     * @var bool|array
     */
    private $warnings = false;

    /**
     * Obtained rates result
     *
     * @var array
     */
    protected $rates = [];

    /**
     * Whether to compare rates
     * If true, the rates function will get all available services with rates
     *
     * @var bool
     */
    protected $isRatesComparison = false;

    /**
     * Whether the shipment plugin has tracking API (non URL)
     *
     * @var bool
     */
    public $isTrackable = false;

    /**
     * Autoload carrier settings
     *
     * @param int|null $customerId Customer ID used to get customer specific settings
     *
     * @see Yii::$app->customerSettings
     *
     */
    abstract public function autoload($customerId = null);

    /**
     * Get Plugin Name
     *
     * @return string
     */
    abstract public function getPluginName();

    /**
     * Prepare Rate Call to Carrier API
     *
     * @return mixed
     */
    abstract protected function ratePrepare();

    /**
     * Execute Rate Curl Request
     *
     * @return $this
     */
    abstract protected function rateExecute();

    /**
     * Rate Process
     *
     * @return $this
     * @throws \Exception
     */
    abstract protected function rateProcess();

    /**
     * Prepare Shipment Call to Carrier API
     *
     * @return mixed
     */
    abstract protected function shipmentPrepare();

    /**
     * Execute Ship Curl Request
     *
     * @return $this
     */
    abstract protected function shipmentExecute();

    /**
     * Shipment Process
     *
     * @return $this
     * @throws \Exception
     */
    abstract protected function shipmentProcess();

    /**
     * Get Rates from carrier API
     *
     * This method will submit the request to shipping carrier to get rates.
     * The rates will be added to Shipment::_rates array
     * including service name, estimated delivery date/time if available and detailed price.
     *
     * @param Shipment $shipment The shipment to rate
     *
     * @return $this
     */
    public function rate(Shipment $shipment)
    {
        $this->shipment = $shipment;

        $this
            ->ratePrepare()
            ->rateExecute()
            ->rateProcess();

        return $this;
    }

    /**
     * Add Warning
     *
     * Adds a warning to the queue. These are more like notices than errors.
     * They are not fatal. Fatal errors will throw Exceptions. You can call
     * getWarnings() to get a list of warnings
     *
     * @param string $message Warning message
     */
    protected function addWarning($message)
    {
        $this->warnings[] = $message;
    }

    /**
     * Get Warnings
     *
     * Returns list of warning from previous run. These are more like notices
     * than errors. They are not fatal. Fatal errors will throw Exceptions.
     *
     * @return array|bool Warnings, or false if none
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Get an array element. If it does not exist, return the default
     *
     * @param array  $array   Array haystack
     * @param string $key     Array key to check
     * @param mixed  $default Default to apply when $key is null
     *
     * @return null
     */
    protected function get(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Ship using carrier API
     *
     * This method will submit the request to shipping carrier to create a shipment
     * and get back the tracking numbers and labels
     *
     * @param Shipment $shipment The shipment to ship
     *
     * @return $this
     * @throws ShipmentException
     */
    public function ship(Shipment $shipment)
    {
        $this->shipment = $shipment;

        $this
            ->shipmentPrepare()
            ->shipmentExecute()
            ->shipmentProcess();

        if (!$this->isShipped) {
            throw new ShipmentException(
                "Shipment was not processed by ShipmentPlugin"
            );
        }

        return $this;
    }
}
