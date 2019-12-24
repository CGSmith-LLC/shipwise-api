<?php

namespace common\models\shipping\extension;

use Yii;
use common\models\shipping\{
    Carrier,
    Service,
    Shipment,
    ShipmentPlugin,
    ShipmentException,
    ShipmentRate};
use \yii\helpers\ArrayHelper;

/**
 * Class UPSPlugin
 *
 * @package common\models\shipping\extension
 */
class UPSPlugin extends ShipmentPlugin
{

    /**
     * Plugin Name
     *
     * @var string Constant
     */
    const PLUGIN_NAME = "UPS";

    /**
     * Development API url
     *
     * @var string
     */
    private $urlDev = 'https://wwwcie.ups.com/webservices/';

    /**
     * Production API url
     *
     * @var string
     */
    private $urlProd = 'https://onlinetools.ups.com/webservices/';

    /**
     * UPS security namespace
     *
     * @var string
     */
    private $securityNamespace = 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0';

    /**
     * Base Tracking URL
     *
     * @var string
     */
    protected $trackingURL = "https://wwwapps.ups.com/tracking/tracking.cgi";

    /**
     * Connection Data
     *
     * @var string
     */
    private $accountNumber;
    private $userId;
    private $password;
    private $key;

    /**
     * Type of Drop off, default value "REGULAR_PICKUP"
     *
     * @var string
     */
    private $dropOffType = "REGULAR_PICKUP";

    /**
     * Multiple piece shipment flag
     *
     * @var bool
     */
    protected $isMps = false;

    /**
     * Current package index in MPS
     *
     * @var integer
     */
    protected $mpsSequenceNumber = 0;

    /**
     * Whether the shipment plugin has tracking API (non URL)
     *
     * @var bool
     */
    public $isTrackable = true;

    /** @inheritdoc */
    public function autoload($customerId = null)
    {
        $_customerId = $customerId ?? $this->shipment->customer_id ?? null;

        $this->isProduction = !(bool)(int)Yii::$app->settings->get('UPS_TEST_MODE', $_customerId);

        $this->setAccountInfo(
            $this->isProduction
                ? Yii::$app->settings->get('UPS_API_ACCOUNT_NUMBER_PROD', $_customerId)
                : Yii::$app->settings->get('UPS_API_ACCOUNT_NUMBER_TEST', $_customerId),

            $this->isProduction
                ? Yii::$app->settings->get('UPS_API_USER_ID_PROD', $_customerId)
                : Yii::$app->settings->get('UPS_API_USER_ID_TEST', $_customerId),

            $this->isProduction
                ? Yii::$app->settings->get('UPS_API_PASSWORD_PROD', $_customerId)
                : Yii::$app->settings->get('UPS_API_PASSWORD_TEST', $_customerId),

            $this->isProduction
                ? Yii::$app->settings->get('UPS_API_KEY_PROD', $_customerId)
                : Yii::$app->settings->get('UPS_API_KEY_TEST', $_customerId)
        );
    }

    /**
     * Set UPS Account Info
     *
     * @param string $accountNumber UPS Account Number
     * @param string $userId        UPS User ID
     * @param string $password      UPS Password
     * @param string $key           UPS Key
     *
     * @return $this
     */
    public function setAccountInfo(
        $accountNumber,
        $userId,
        $password,
        $key
    ) {
        $this->accountNumber = $accountNumber;
        $this->userId        = $userId;
        $this->key           = $key;
        $this->password      = $password;

        return $this;
    }

    public function setAccountNumber($value)
    {
        $this->accountNumber = $value;
    }

    /**
     * Set Drop Off Type
     *
     * @param string $type FedEx drop-off type
     *
     * @return $this
     */
    public function setDropOffType($type)
    {
        $this->dropOffType = $type;

        return $this;
    }

    /**
     * Get Plugin Name
     *
     * @return string
     */
    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Prepare Shipment Rate Call to carrier API
     *
     * @return $this
     * @version 2018.11.04
     */
    protected function ratePrepare()
    {

        $fromCountry = $this->shipment->senderCountry->code;
        $toCountry   = $this->shipment->recipientCountry->code;

        /**
         * Build UPS `RateRequest`
         */
        $this->data = [

            'Request' => [
                'RequestOption' => $this->isRatesComparison ? 'Shop' : 'Rate',
            ],

            'Shipment' => [

                'Shipper' => [
                    'Name' => substr(
                        $this->shipment->sender_company ?? $this->shipment->sender_contact,
                        0,
                        35),

                    'ShipperNumber' => $this->accountNumber ?? $this->shipment->service->carrierAccount->number ?? null,

                    'Address' => [
                        'AddressLine'       => [
                            $this->shipment->sender_address1,
                            $this->shipment->sender_address2,
                        ],
                        'City'              => $this->shipment->sender_city,
                        'StateProvinceCode' => $this->shipment->sender_state,
                        'PostalCode'        => $this->shipment->sender_postal_code,
                        'CountryCode'       => $fromCountry,
                    ],
                ],

                'ShipTo' => [
                    'Name' => substr(
                        $this->shipment->recipient_company ?? $this->shipment->recipient_contact,
                        0,
                        35),

                    'ShipperNumber' => $this->accountNumber ?? $this->shipment->service->carrierAccount->number ?? null,

                    'Address' => [
                        'AddressLine'       => [
                            $this->shipment->recipient_address1,
                            $this->shipment->recipient_address2,
                        ],
                        'City'              => $this->shipment->recipient_city,
                        'StateProvinceCode' => $this->shipment->recipient_state,
                        'PostalCode'        => $this->shipment->recipient_postal_code,
                        'CountryCode'       => $toCountry,
                    ],

                    'ResidentialAddressIndicator' => (bool)$this->shipment->recipient_is_residential,
                ],

                'ShipFrom' => [
                    'Name' => substr(
                        $this->shipment->sender_company ?? $this->shipment->sender_contact,
                        0,
                        35),

                    'Address' => [
                        'AddressLine'       => [
                            $this->shipment->sender_address1,
                            $this->shipment->sender_address2,
                        ],
                        'City'              => $this->shipment->sender_city,
                        'StateProvinceCode' => $this->shipment->sender_state,
                        'PostalCode'        => $this->shipment->sender_postal_code,
                        'CountryCode'       => $fromCountry,
                    ],
                ],

                'Service' => [
                    'Code' => $this->shipment->service->carrier_code ?? null,
                ],
            ],
        ];

        if ($fromCountry == 'US') {
            $this->data['CustomerClassification'] = [
                'Code' => '00', // 00 = Rates Associated with Shipper Number
            ];
        }

        /**
         * Shipment items
         */
        foreach ($this->shipment->packages as $package) {
            $this->data['Shipment']['Package'][] = [
                'PackagingType' => [
                    'Code' => $this->shipment->packageType->carrier_code ?? '00', // 00 = UNKNOWN
                ],

                'Dimensions' => [
                    'UnitOfMeasurement' => [
                        'Code' => $this->shipment->dim_units,
                    ],

                    'Length' => round($package->length),
                    'Width'  => round($package->width),
                    'Height' => round($package->height),
                ],

                'PackageWeight' => [
                    'UnitOfMeasurement' => [
                        'Code' => $this->shipment->weight_units . 'S',
                    ],

                    'Weight' => round($package->weight),
                ],
            ];
        }

        return $this;
    }

    /**
     * Execute Rate API Request
     *
     * @return $this
     * @throws ShipmentException
     * @throws \SoapFault
     * @version 2018.11.04
     */
    protected function rateExecute()
    {
        $url = ($this->isProduction)
            ? $this->urlProd
            : $this->urlDev;

        $this->response = $this->runPostCall(
            "{$url}Rate",
            $this->data,
            'ProcessRate',
            __DIR__ . '/wsdl/ups/RateWS.wsdl'
        );

        return $this;
    }

    /**
     * Rate process
     *
     * This method will process the response received from carrier API.
     * In a successful response it will set the Shipment::_rates array
     *
     * @return $this
     * @version 2018.11.04
     *
     */
    protected function rateProcess()
    {
        if (!isset($this->response->Response)) {
            return $this;
        }

        /**
         * NOTE/WARNING Case
         *
         * This status means that the request went through but UPS has some
         * relevant notes to communicate to us. Retrieve notification messages
         * and continue.
         */
        if (isset($this->response->Response->Alert)) {
            // Object case
            if (isset($this->response->Response->Alert->Description)) {
                $this->addWarning($this->response->Response->Alert->Description);

                // Array of objects case
            } else {
                if (is_array($this->response->Response->Alert) && count($this->response->Response->Alert) > 0) {
                    foreach ($this->response->Response->Alert as $alert) {
                        if (isset($alert->Description)) {
                            $this->addWarning($alert->Description);
                        }
                    }
                }
            }
        }

        /**
         * SUCCESS Case
         *
         * This status means that our rate request was successful.
         * Retrieve available rates and add them to Shipment::_rates
         */
        if (isset($this->response->Response->ResponseStatus) && isset($this->response->Response->ResponseStatus->Code)
            && ($this->response->Response->ResponseStatus->Code == '1')) {

            /** @var array $rates Rate object(s) returned by API */
            $rates = [];
            if (isset($this->response->RatedShipment) && isset($this->response->RatedShipment->Service)) { // one object returned
                $rates[] = $this->response->RatedShipment;
            } else {
                if (isset($this->response->RatedShipment) && is_array($this->response->RatedShipment)
                    && count($this->response->RatedShipment) > 0) { // array of objects
                    $rates = $this->response->RatedShipment;
                }
            }

            foreach ((array)$rates as $rate) {
                if (isset($rate->Service->Code) && isset($rate->TotalCharges)) {

                    $service = CarrierService::find()
                        ->forCarrierService($this->shipment->carrier->id, $rate->Service->Code)
                        ->one();

                    $this->shipment->addRate(
                        new ShipmentRate(
                            $rate->Service->Code ?? null,
                            $service->name ?? $rate->Service->Description ?? null,
                            $service->getIcon(),
                            $rate->TotalCharges->MonetaryValue ?? null,
                            $rate->TransportationCharges->MonetaryValue ?? null,
                            $rate->ServiceOptionsCharges->MonetaryValue ?? null
                        )
                    );
                }
            }


        }

        return $this;
    }

    /**
     * Run Post Call
     *
     * Run a call to the shipper using SoapClient
     *
     * This method overrides parent method
     *
     * @param string     $url           Carrier API Url
     * @param null|mixed $data          Data to pass to API
     * @param string     $operationName Operation name to be called by Soap
     * @param string     $wsdl          Path to wsdl file
     *
     * @return string Return data
     * @throws ShipmentException Error
     * @throws \SoapFault
     * @see ShipmentConnection:runPostCall()
     *
     */
    public function runPostCall(
        $url,
        $data = null,
        $operationName = null,
        $wsdl = null
    ) {
        ini_set("soap.wsdl_cache_enabled", "0");

        // Make sure we have soap installed
        if (!extension_loaded('soap')) {
            throw new \LogicException("Soap is not installed");
        }

        /**
         * @var \SoapClient $soapClient
         */
        $soapClient = new \SoapClient(
            $wsdl,
            [
                'trace'              => !$this->isProduction,
                'connection_timeout' => $this->curlDownloadTimeoutInSeconds,
                'soap_version'       => 'SOAP_1_1',
            ]
        );

        // Create UPS security soap header
        $header = new \SoapHeader($this->securityNamespace, 'UPSSecurity', [
            'UsernameToken'      => [
                'Username' => $this->userId,
                'Password' => $this->password,
            ],
            'ServiceAccessToken' => [
                'AccessLicenseNumber' => $this->key,
            ],
        ]);

        //Yii::debug($header, 'UPS Header');

        $response = null;

        try {
            // Set the endpoint
            $soapClient->__setLocation($url);

            // Set the security header
            $soapClient->__setSoapHeaders($header);

            // Invoke UPS web service operation
            $response = $soapClient->{$operationName}($data);

        } catch (\SoapFault $e) {
            //Yii::debug($e, 'UPS Soap Fault');

            /**
             * FAILURE/ERROR Case
             *
             * UPS errors are returned as SOAP fault
             */
            $msg = $e->faultstring . " [{$e->faultcode}]";
            if (isset($e->detail->Errors)) {

                // Object case
                if (isset($e->detail->Errors->ErrorDetail->PrimaryErrorCode)) {
                    $msg .= "\n" . @$e->detail->Errors->ErrorDetail->PrimaryErrorCode->Description . ' ' .
                        '[' . @$e->detail->Errors->ErrorDetail->PrimaryErrorCode->Code . ']';

                    // Array of objects case
                } else {
                    if (is_array($e->detail->Errors) && count($e->detail->Errors) > 0) {
                        foreach ($e->detail->Errors as $error) {
                            if (isset($error->ErrorDetail->PrimaryErrorCode)) {
                                $msg .= "\n" . @$error->ErrorDetail->PrimaryErrorCode->Description . ' ' .
                                    '[' . @$error->ErrorDetail->PrimaryErrorCode->Code . ']';
                            }
                        }
                    }
                }
            }
            throw new ShipmentException($msg);
        }

        //Yii::debug($response, 'UPS Response');

        return $response;
    }
}
