<?php

namespace common\models\shipping\extension;

use common\models\{Charge, Money};
use Yii;
use common\models\shipping\{Carrier, PackageType, Service, ShipmentPlugin, ShipmentException, ShipmentRate};

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

    /**
     * Time in transit response from UPS stored as [UPS Code => Time in transit]
     *
     * @var array
     */
    public $timeInTransitResponse;

    /** @inheritdoc */
    public function autoload($customerId = null)
    {
        $this->isProduction = !(bool)(int)Yii::$app->customerSettings->get('ups_api_test_mode', $customerId);

        $this->setAccountInfo(
            $this->isProduction
                ? Yii::$app->customerSettings->get('ups_api_account', $customerId)
                : Yii::$app->customerSettings->get('ups_api_account_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('ups_api_user_id', $customerId)
                : Yii::$app->customerSettings->get('ups_api_user_id_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('ups_api_password', $customerId)
                : Yii::$app->customerSettings->get('ups_api_password_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('ups_api_key', $customerId)
                : Yii::$app->customerSettings->get('ups_api_key_test', $customerId)
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
     * Check alerts before continuing
     */
    private function checkAlerts()
    {
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
    }

    /**
     * Prepare Shipment Rate Call to carrier API
     *
     * @return $this
     * @version 2019.12.20
     */
    protected function ratePrepare()
    {

        /**
         * Build UPS `RateRequest`
         */
        $this->data = [

            'Request' => [
                // Rates comparison or specific service
                'RequestOption' => $this->shipment->service ? 'Rate' : 'Shop',
            ],

            'Shipment' => [
                'ShipmentRatingOptions' => [
                    'NegotiatedRatesIndicator' => "",
                ],
                'Shipper' => [
                    'Name' => substr(
                        $this->shipment->sender_company ?? $this->shipment->sender_contact,
                        0,
                        35),

                    'ShipperNumber' => $this->accountNumber,

                    'Address' => [
                        'AddressLine'       => [
                            $this->shipment->sender_address1,
                            $this->shipment->sender_address2,
                        ],
                        'City'              => $this->shipment->sender_city,
                        'StateProvinceCode' => $this->shipment->sender_state,
                        'PostalCode'        => $this->shipment->sender_postal_code,
                        'CountryCode'       => $this->shipment->sender_country,
                    ],
                ],

                'ShipTo' => [
                    'Name' => substr(
                        $this->shipment->recipient_company ?? $this->shipment->recipient_contact,
                        0,
                        35),

                    'ShipperNumber' => $this->accountNumber,

                    'Address' => [
                        'AddressLine'       => [
                            $this->shipment->recipient_address1,
                            $this->shipment->recipient_address2,
                        ],
                        'City'              => $this->shipment->recipient_city,
                        'StateProvinceCode' => $this->shipment->recipient_state,
                        'PostalCode'        => $this->shipment->recipient_postal_code,
                        'CountryCode'       => $this->shipment->recipient_country,
                    ],
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
                        'CountryCode'       => $this->shipment->sender_country,
                    ],
                ],
            ],
        ];

        if ((bool)$this->shipment->recipient_is_residential) {
            $this->data['Shipment']['ShipTo']['Address']['ResidentialAddressIndicator'] = (bool)$this->shipment->recipient_is_residential;
        }

        /**
         * Rates comparison or specific service
         */
        if ($this->shipment->service) {
            $this->data['Shipment']['Service']['Code'] = $this->shipment->service->carrier_code ?? null;
        }

        /**
         * Type of rates
         */
        if ($this->shipment->sender_country == 'US') {
            $this->data['CustomerClassification'] = [
                /**
                 * 00 = Rate associated with account number (as long as account number valid
                 * 01 = Daily rates
                 * 04 = Retail rates
                 * 53 = Standard list rates
                 */
                'Code' => '00',
            ];
        }

        /**
         * Shipment items
         */
        foreach ($this->shipment->getPackages() as $package) {
            $this->data['Shipment']['Package'][] = [
                'PackagingType' => [
                    'Code' => PackageType::map(Carrier::UPS, $this->shipment->package_type) ?? '00', // 00 = UNKNOWN,
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
                // @todo add here "PackageServiceOptions" if needed.
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
     * @version 2019.12.20
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

        Yii::debug($this->data, 'UPS API Request');
        Yii::debug($this->response, 'UPS API Response');

        return $this;
    }

    /**
     * Rate process
     *
     * This method will process the response received from carrier API.
     * In a successful response it will set the Shipment::_rates array
     *
     * @return $this
     * @version 2019.12.20
     *
     */
    protected function rateProcess()
    {
        if (!isset($this->response->Response)) {
            return $this;
        }
        $this->checkAlerts();

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

                    $service = Service::find()
                        ->forCarrierService($this->shipment->carrier->id, $rate->Service->Code)
                        ->one();

                    $_rate = new ShipmentRate();

                    $_rate->infoMessage = $rate->RatedShipmentAlert->Description ?? null;

                    $_rate->serviceCode = $service->shipwise_code ?? $rate->Service->Code ?? null;
                    $_rate->serviceName = $service->name ?? $rate->Service->Description ?? null;


                    if (isset($rate->NegotiatedRateCharges->TotalCharge)) {
                        $_rate->totalPrice = new Money([
                            'amount'   => $rate->NegotiatedRateCharges->TotalCharge->MonetaryValue ?? null,
                            'currency' => $rate->NegotiatedRateCharges->TotalCharge->CurrencyCode ?? null,
                        ]);
                    }else {
                        $_rate->totalPrice = new Money([
                            'amount'   => $rate->TotalCharges->MonetaryValue ?? null,
                            'currency' => $rate->TotalCharges->CurrencyCode ?? null,
                        ]);
                    }



                    if (isset($rate->NegotiatedRateCharges->TotalCharge)) {
                        $_rate->addCharge(new Charge([
                            'type'         => 'BASE',
                            'description'  => 'Negotiated price',
                            'amount'       => new Money([
                                'amount'   => $rate->NegotiatedRateCharges->TotalCharge->MonetaryValue ?? null,
                                'currency' => $rate->NegotiatedRateCharges->TotalCharge->CurrencyCode ?? null,
                            ]),
                        ]));
                    } else {
                        $_rate->addCharge(new Charge([
                            'type'         => 'BASE',
                            'description'  => 'Base price',
                            'amount'       => new Money([
                                'amount'   => $rate->TransportationCharges->MonetaryValue ?? null,
                                'currency' => $rate->TransportationCharges->CurrencyCode ?? null,
                            ]),
                        ]));
                    }

                    // @todo for Surcharges see "PackageServiceOptions" in request.

                    /**
                     * If business days in transit is not set make a request to Time In Transit API
                     * Store response for future in case something else is missing transit time
                     */
                    $_rate->transitTime = $rate->GuaranteedDelivery->BusinessDaysInTransit ?? null;
                    if (is_null($_rate->transitTime) && !isset($this->timeInTransitResponse)) {
                        $this->timeInTransitExecute()->timeInTransitProcess();
                        $_rate->transitTime = $this->findTransitTime($_rate->serviceCode);
                    } elseif (is_null($_rate->transitTime) && isset($this->timeInTransitResponse)) {
                        $_rate->transitTime = $this->findTransitTime($_rate->serviceCode);
                    }

                    $_rate->deliveryTimeStamp = null; // n/a for UPS
                    $_rate->deliveryDayOfWeek = null; // n/a for UPS
                    $_rate->deliveryByTime    = $rate->GuaranteedDelivery->DeliveryByTime ?? null;

                    $this->shipment->addRate($_rate);
                }
            }
        }

        return $this;
    }

    /**
     * Prepare variables and execute SOAP request to get transit times
     *
     * @return $this
     * @throws ShipmentException
     * @throws \SoapFault
     */
    protected function timeInTransitExecute()
    {
        $url = ($this->isProduction) ? $this->urlProd : $this->urlDev;

        Yii::debug($this->shipment);
        $data = [
            'Request' => [], // need empty array or option set
            'ShipFrom' => [
                'Address' => [
                    'AddressLine'       => [
                        $this->shipment->sender_address1,
                        $this->shipment->sender_address2,
                    ],
                    'City'              => $this->shipment->sender_city,
                    'StateProvinceCode' => $this->shipment->sender_state,
                    'PostalCode'        => $this->shipment->sender_postal_code,
                    'CountryCode'       => $this->shipment->sender_country,
                ],
            ],
            'ShipTo' => [
                'Address' => [
                    'AddressLine'       => [
                        $this->shipment->recipient_address1,
                        $this->shipment->recipient_address2,
                    ],
                    'City'              => $this->shipment->recipient_city,
                    'StateProvinceCode' => $this->shipment->recipient_state,
                    'PostalCode'        => $this->shipment->recipient_postal_code,
                    'CountryCode'       => $this->shipment->recipient_country,
                ],
                'ResidentialAddressIndicator' => (bool)$this->shipment->recipient_is_residential,
            ],
            'Pickup' => [
                'Date' =>  (new \DateTime($this->shipment->shipment_date))->format('Ymd')
            ]
        ];


        $this->response = $this->runPostCall(
            "{$url}TimeInTransit",
            $data,
            'ProcessTimeInTransit',
            __DIR__ . '/wsdl/ups/TNTWS.wsdl'
        );

        return $this;
    }

    /**
     * Process time in transit request and store as array in $this->timeInTransitReponse
     *
     * @return $this
     */
    protected function timeInTransitProcess()
    {
        if (!isset($this->response->Response)) {
            return $this;
        }
        $this->checkAlerts();

        /**
         * SUCCESS Case
         *
         * This status means that our time in transit request was successful.
         * Retrieve available transit times and store them to $this->timeInTransitResponse
         */
        if (isset($this->response->Response->ResponseStatus) && isset($this->response->Response->ResponseStatus->Code)
            && ($this->response->Response->ResponseStatus->Code == '1')
            && !isset($this->response->CandidateResponse)) {
            $services = $this->response->TransitResponse->ServiceSummary;
            foreach ((array)$services as $service) {
                $this->timeInTransitResponse[$service->Service->Code] = $service->EstimatedArrival->BusinessDaysInTransit;
            }
        } else {
            $this->addWarning('No transit time available for ' . $this->shipment->recipient_postal_code . '. Check if valid ZIP');
        }
    }




    /**
     * Find transit based off of UPS code
     *
     * @param $code string UPS code
     * @return $transitTime integer Transit time as integer is returned
     */
    protected function findTransitTime($code)
    {
        $transitTime = 0;

        switch ($code) {
            case 'UPSNextDayAirEarlyAM':
                $transitTime = $this->timeInTransitResponse['1DM'];
                break;
            case 'UPSNextDayAir':
                $transitTime = $this->timeInTransitResponse['1DA'];
                break;
            case 'UPSNextDayAirSaver':
                $transitTime = $this->timeInTransitResponse['1DP'];
                break;
            case 'UPS2ndDayAirAM':
                $transitTime = $this->timeInTransitResponse['2DM'];
                break;
            case 'UPS2ndDayAir':
                $transitTime = $this->timeInTransitResponse['2DA'];
                break;
            case 'UPSGround':
                $transitTime = $this->timeInTransitResponse['GND'];
                break;
            case 'UPS3DaySelect':
                $transitTime = $this->timeInTransitResponse['3DS'];
                break;
        }

        return $transitTime;
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
                'trace'              => 1,//!$this->isProduction,
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
            Yii::debug($soapClient->__getLastRequest());
            Yii::debug($soapClient->__getLastResponse());
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
