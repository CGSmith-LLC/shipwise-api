<?php

namespace common\models\shipping\extension;

use common\models\{Charge, Money};
use Yii;
use common\models\shipping\{Carrier,
    PackageType,
    Service,
    ShipmentPackage,
    ShipmentPlugin,
    ShipmentException,
    ShipmentRate
};

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
                'Shipper'               => [
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

        Yii::debug($this->data, 'UPS Rate Request');
        Yii::debug($this->response, 'UPS Rate Response');

        return $this;
    }

    /**
     * Rate process
     *
     * This method will process the response received from carrier API.
     * In a successful response it will set the Shipment::_rates array
     *
     * @return $this
     * @throws ShipmentException
     * @throws \SoapFault
     * @version 2019.12.20
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
                    } else {
                        $_rate->totalPrice = new Money([
                            'amount'   => $rate->TotalCharges->MonetaryValue ?? null,
                            'currency' => $rate->TotalCharges->CurrencyCode ?? null,
                        ]);
                    }


                    if (isset($rate->NegotiatedRateCharges->TotalCharge)) {
                        $_rate->addCharge(new Charge([
                            'type'        => 'BASE',
                            'description' => 'Negotiated price',
                            'amount'      => new Money([
                                'amount'   => $rate->NegotiatedRateCharges->TotalCharge->MonetaryValue ?? null,
                                'currency' => $rate->NegotiatedRateCharges->TotalCharge->CurrencyCode ?? null,
                            ]),
                        ]));
                    } else {
                        $_rate->addCharge(new Charge([
                            'type'        => 'BASE',
                            'description' => 'Base price',
                            'amount'      => new Money([
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
            'Request'  => [], // need empty array or option set
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
            'ShipTo'   => [
                'Address'                     => [
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
            'Pickup'   => [
                'Date' => (new \DateTime($this->shipment->shipment_date))->format('Ymd'),
            ],
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
     *
     * @return integer $transitTime Transit time as integer is returned
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

        Yii::debug($response, 'UPS Response');

        return $response;
    }

    /**
     * Prepare Shipment Call to carrier API
     *
     * This function builds UPS shipment request
     *
     * @return $this
     * @throws \Exception
     * @version 2020.03.01
     */
    protected function shipmentPrepare()
    {
        // Determine if MPS (Multiple Piece Shipment)
        $this->isMps = (count($this->shipment->getPackages()) > 1);

        /**
         * Build UPS `ShipmentRequest` array
         */
        $this->data = [

            'Request' => [
                'RequestOption' => 'nonvalidate',
            ],

            'Shipment' => [

                'Shipper' => [
                    'Name'                    => substr($this->shipment->sender_company, 0, 35),
                    'AttentionName'           => substr($this->shipment->sender_contact, 0, 35),
                    'TaxIdentificationNumber' => $this->shipment->sender_tax_id,
                    'ShipperNumber'           => $this->accountNumber,

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

                    'Phone' => [
                        'Number'    => preg_replace("/[^0-9]/", "", $this->shipment->sender_phone),
                        'Extension' => $this->shipment->sender_phone_ext,
                    ],

                    'EMailAddress' => $this->shipment->sender_email,
                ],

                'ShipTo' => [
                    'Name' => substr(
                        $this->shipment->recipient_company ?? $this->shipment->recipient_contact,
                        0,
                        35),

                    'AttentionName'           => substr($this->shipment->recipient_contact, 0, 35),
                    'TaxIdentificationNumber' => $this->shipment->recipient_tax_id,
                    'ShipperNumber'           => $this->accountNumber,

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

                    'Phone' => [
                        'Number'    => preg_replace("/[^0-9]/", "", $this->shipment->recipient_phone),
                        'Extension' => $this->shipment->recipient_phone_ext,
                    ],

                    'EMailAddress' => $this->shipment->recipient_email,

                    'ResidentialAddressIndicator' => (bool)$this->shipment->recipient_is_residential,
                ],

                'PaymentInformation' => [
                    'ShipmentCharge' => [
                        'Type'        => '01', // 01 = Transportation, 02 = Duties and Taxes, 03 = Broker of Choice
                        'BillShipper' => [
                            'AccountNumber' => $this->shipment->bill_transport_account_num
                                ?? $this->accountNumber
                                ?? null,
                        ],
                    ],
                ],

                'Service' => [
                    'Code' => $this->shipment->service->carrier_code ?? null,
                ],

                'ShipmentServiceOptions' => [

                    /*
                     * NotificationCode
                     * ----------------
                     * 5 - QV In-transit Notification
                     * 6 - QV Ship Notification
                     * 7 - QV Exception Notification
                     * 8 - QV Delivery Notification
                     * 2 - Return Notification or Label Creation Notification
                     * 012 - Alternate Delivery Location Notification
                     * 013 - UAP Shipper Notification.
                     */
                    'Notification' => [
                        // Notify shipper on Exception
                        [
                            'NotificationCode' => '7',
                            'EMail'            => [
                                'EMailAddress' => $this->shipment->sender_email,
                            ],
                            'Locale'           => [
                                'Language' => 'ENG',
                                'Dialect'  => 'US',
                            ],
                        ],
                    ],
                ],
            ],

            'LabelSpecification' => [
                'LabelImageFormat' => [
                    'Code' => 'GIF',
                ],
                'LabelStockSize'   => [
                    'Height' => '6',
                    'Width'  => '4',
                ],
            ],
        ];

        /**
         * Shipment items
         */
        foreach ($this->shipment->getPackages() as $package) {
            $_pkg = [

                'Description' => $package->description ?? 'Package description', // Merchandise description of package
                'Packaging'   => [
                    /*
                     * Package types:
                     * --------------
                     * 01 = UPS Letter
                     * 02 = Customer Supplied Package
                     * 03 = Tube
                     * 04 = PAK
                     * 21 = UPS Express Box
                     * 24 = UPS 25KG Box
                     * 25 = UPS 10KG Box
                     * 30 = Pallet
                     * 2a = Small Express Box
                     * 2b = Medium Express Box
                     * 2c = Large Express Box
                     * 56 = Flats
                     * 57 = Parcels
                     * 58 = BPM
                     * 59 = First Class
                     * 60 = Priority
                     * 61 = Machineables
                     * 62 = Irregulars
                     * 63 = Parcel Post
                     * 64 = BPM Parcel
                     * 65 = Media Mail
                     * 66 = BPM Flat
                     * 67 = Standard Flat
                     */
                    'Code' => PackageType::map(Carrier::UPS, $this->shipment->package_type) ?? '02',
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

            /**
             * Package references. Max Allowed: 2
             * For available ref codes see UPS developer guide, search for "Reference Number Codes"
             *
             * @todo If needed, match your references with UPS codes, eg. invoice number, customer account, etc.
             */
            if ($package->reference1) {
                $_pkg['ReferenceNumber'][] = [
                    'Code'  => 'TN', // TN = Transaction Reference Number
                    'Value' => $package->reference1,
                ];
            }
            if ($package->reference2) {
                $_pkg['ReferenceNumber'][] = [
                    'Code'  => 'IK', // IK = Invoice Number
                    'Value' => $package->reference2,
                ];
            }

            $this->data['Shipment']['Package'][] = $_pkg;
        }

        /**
         * Indicates a shipment contains written, typed, or printed communication of no commercial value.
         */
        if ($this->shipment->package_contents == 'DOCUMENTS') {
            $this->data['Shipment']['DocumentsOnlyIndicator'] = '1';
        }

        /**
         * International shipments
         *
         * Append customs clearance details
         */
        if ($this->shipment->recipient_country != $this->shipment->sender_country) {

            $this->data['Shipment']['Description'] = 'Dental impressions'; // @todo Adjust at your needs

            /** @var array $intForms International Forms information */
            $intForms = [];

            $intForms['FormType'] = '01'; // 01 = Invoice

            /**
             * "Forward" shipment type
             */
            //if ($this->shipment->shipment_type == Shipment::TYPE_FORWARD) { // @todo Implement your logic here if needed
            $invoiceDate                 = new \DateTime($this->shipment->shipment_date);
            $intForms['InvoiceDate']     = $invoiceDate->format('Ymd');
            $intForms['ReasonForExport'] = 'TEMPORARY EXPORT';

            $intForms['Contacts']['SoldTo'] = [
                'Name'          => substr($this->shipment->recipient_company, 0, 35),
                'AttentionName' => substr($this->shipment->recipient_contact, 0, 35),
                'Phone'         => [
                    'Number'    => $this->shipment->recipient_phone,
                    'Extension' => $this->shipment->recipient_phone_ext,
                ],

                'Address' => [
                    'AddressLine'       => [
                        $this->shipment->recipient_address1,
                        $this->shipment->recipient_address2,
                    ],
                    'City'              => $this->shipment->recipient_city,
                    'StateProvinceCode' => $this->shipment->recipient_state,
                    'PostalCode'        => $this->shipment->recipient_postal_code,
                    'CountryCode'       => $this->shipment->recipient_country,
                    'EMailAddress'      => $this->shipment->recipient_email,
                ],
            ];
            //}

            if ($this->shipment->sender_country == 'US') {
                //$intForms['FormType'] = '11'; // 11 = EEI

                if (in_array($this->shipment->recipient_country, ['CA', 'PR'])) {
                    // Required for forward shipments whose origin is the US and destination is Puerto Rico or Canada
                    $this->data['Shipment']['InvoiceLineTotal'] = [
                        'CurrencyCode'  => 'USD',
                        'MonetaryValue' => '1',
                    ];
                }
            }

            if (in_array($this->shipment->sender_country, ['CA', 'PR']) && $this->shipment->recipient_country == 'US') {
                $intForms['FormType'] = '05'; // 05 = Partial Invoice
            }

            if ($intForms['FormType'] == '11') { // EEI
                $intForms['EEIFilingOption'] = [
                    'Code'         => '1', // 1 = Shipper filed, 2 = AES Direct, 3 = UPS filed
                    'EMailAddress' => $this->shipment->sender_email,
                ];
            }

            // @todo For now this is hardcoded. See the commented block below with $commodities for dynamic population.
            $intForms['Product'][] = [
                'Description' => 'Dental Impressions for temp. export', // // @todo Adjust at your needs

                'Unit' => [
                    'Number'            => $this->shipment->getPackages()[0]->quantity ?? 1,
                    'UnitOfMeasurement' => [
                        'Code' => 'PCS',
                    ],
                    'Value'             => '5',
                ],

                'CommodityCode'     => '3407.00',
                'PartNumber'        => '123',
                'OriginCountryCode' => $this->shipment->sender_country,
            ];

            $intForms['CurrencyCode'] = $this->shipment->currency;

            /*$commodities = [];
            foreach (
                $this->shipment->getCustomClearanceDetail()->commodities
                as $commodity
            ) {
                $commodities[] = [
                    'NumberOfPieces'       => $commodity->numberOfPieces,
                    'Description'          => $commodity->description,
                    'CountryOfManufacture' => $commodity->countryOfManufacture,
                    'Weight'               => [
                        'Units' => $commodity->weight->units,
                        'Value' => $commodity->weight->value,
                    ],
                    'Quantity'             => $commodity->quantity,
                    'QuantityUnits'        => $commodity->quantityUnits,
                    'UnitPrice'            => [
                        'Currency' => $this->shipment->getCustomClearanceDetail()->customsValue->currency,
                        'Amount'   => $commodity->unitPrice,
                    ],
                    'CustomsValue'         => [
                        'Currency' => $this->shipment->getCustomClearanceDetail()->customsValue->currency,
                        'Amount'   => $commodity->customsValue,
                    ],
                ];
            }
            $this->data['RequestedShipment']['CustomsClearanceDetail'] = [
                'DutiesPayment'   => [
                    'PaymentType' => $this->shipment->bill_duties_to,
                    'Payor'       => [
                        'ResponsibleParty' => [
                            'AccountNumber' => $this->shipment->bill_duties_account_num
                                $this->accountNumber
                                ?? $this->shipment->service->carrierAccount->number
                                ?? null,
                            'Contact'       => null,
                            'Address'       => [
                                'CountryCode' => $fromCountry,
                            ],
                        ],
                    ],
                ],
                'DocumentContent' => $this->shipment->getCustomClearanceDetail()->documentContent,
                'CustomsValue'    => [
                    'Currency' => $this->shipment->getCustomClearanceDetail()->customsValue->currency,
                    'Amount'   => $this->shipment->getCustomClearanceDetail()->customsValue->amount,
                ],
                'Commodities'     => $commodities,
                'ExportDetail'    => [
                    'B13AFilingOption' => $this->shipment->getCustomClearanceDetail()->b13AFilingOption,
                ],
            ];*/

            $this->data['Shipment']['ShipmentServiceOptions']['InternationalForms'] = $intForms;

        } // End if international

        //Yii::debug($this->data, 'UPS Ship Request');

        return $this;
    }

    /**
     * Execute the API Request
     *
     * @return $this
     * @throws ShipmentException
     * @throws \SoapFault
     * @version 2020.03.01
     */
    protected function shipmentExecute()
    {
        $url = ($this->isProduction)
            ? $this->urlProd
            : $this->urlDev;

        $this->response = $this->runPostCall(
            "{$url}Ship",
            $this->data,
            'ProcessShipment',
            __DIR__ . '/wsdl/ups/Ship.wsdl'
        );

        return $this;
    }

    /**
     * Shipment process
     *
     * This method will process the response received from carrier API
     *
     * @return $this
     * @version 2020.03.01
     *
     */
    protected function shipmentProcess()
    {
        if (!isset($this->response->Response)) {
            return $this;
        }

        /**
         * NOTE/WARNING Case
         *
         * This status means that the shipment went through but UPS has some
         * relevant notes to communicate to us. Retrieve notification messages
         * and continue.
         */
        if (isset($this->response->Response->Alert)) {

            $alerts = [];
            if (isset($this->response->Response->Alert->Description)) { // one object
                $alerts[] = $this->response->Response->Alert;

            } else { // array of objects
                if (is_array($this->response->Response->Alert) && count($this->response->Response->Alert) > 0) {
                    $alerts = (array)$this->response->Response->Alert;
                }
            }
            foreach ($alerts as $alert) {
                $msg = @$alert->Description . " [" . @$alert->Code . "]";
                $this->addWarning($msg);
            }
        }

        /**
         * SUCCESS Case
         *
         * This status means that our shipment request was successful.
         * Retrieve tracking and label details from completed shipment reply.
         */
        if (isset($this->response->Response->ResponseStatus) && isset($this->response->Response->ResponseStatus->Code)
            && ($this->response->Response->ResponseStatus->Code == '1')) {

            if (isset($this->response->ShipmentResults->ShipmentIdentificationNumber)) {
                $this->shipment->external_id1 = $this->response->ShipmentResults->ShipmentIdentificationNumber;
            }

            /**
             * Shipping labels and tracking info
             */
            /** @var array $packageResults Array of package objects returned by API */
            $packageResults = [];
            if (isset($this->response->ShipmentResults->PackageResults->TrackingNumber)) { // one object returned
                $packageResults[] = $this->response->ShipmentResults->PackageResults;
            } else {
                if (isset($this->response->ShipmentResults->PackageResults)
                    && is_array($this->response->ShipmentResults->PackageResults)
                    && count($this->response->ShipmentResults->PackageResults) > 0) { // array of objects
                    $packageResults = $this->response->ShipmentResults->PackageResults;
                }
            }

            /** @var ShipmentPackage[] Shipment packages */
            $packages = $this->shipment->getPackages();

            /** @var array Array of filenames for temporary created label files to be merged into one */
            $tmpFiles = [];

            foreach ($packages as $idx => &$package) {

                if (isset($packageResults[$idx])) {

                    // Tracking details
                    $package->tracking_num        = $packageResults[$idx]->TrackingNumber ?? null;
                    $package->master_tracking_num = $this->shipment->packages[0]->tracking_num ?? null;
                    $package->tracking_url        = "{$this->trackingURL}?tracknum=" . $package->tracking_num;

                    // Label data
                    if (isset($packageResults[$idx]->ShippingLabel)) {
                        $package->label_data   = $packageResults[$idx]->ShippingLabel->GraphicImage ?? null;
                        $package->label_format = $packageResults[$idx]->ShippingLabel->ImageFormat->Code ?? null;
                        /**
                         * Convert obtained label from UPS to the correct format:
                         *  - Rotate the GIF label 90 degrees clockwise
                         */
                        $filename = 'tmp_' . $package->tracking_num . '.' . $package->label_format;
                        $fp       = fopen($filename, 'wb');
                        fwrite($fp, base64_decode($package->label_data));
                        fclose($fp);
                        // using ImageMagick here for rotating the GIF
                        exec("convert -rotate \"90\" {$filename} {$filename}");
                        $tmpFiles[] = $filename;
                    }
                }
            }

            /**
             * Convert GIF to PDF and merge into one file, then delete all temp files.
             */
            if (!empty($tmpFiles)) {
                $mergedFilename = $packages[0]->master_tracking_num . ".pdf";
                // using ImageMagick here for merging GIFs into one PDF file
                exec("convert " . implode(" ", $tmpFiles) . " $mergedFilename");
                $this->shipment->mergedLabelsData   = base64_encode(file_get_contents($mergedFilename));
                $this->shipment->mergedLabelsFormat = 'PDF';
                @unlink($mergedFilename);
                foreach ($tmpFiles as $filename) {
                    @unlink($filename);
                }
            }

            /**
             * International Forms
             * (commercial invoice)
             */
            if (isset($this->response->ShipmentResults->Form) && isset($this->response->ShipmentResults->Form->Image)) {
                $ciFormat = $this->response->ShipmentResults->Form->Image->ImageFormat->Code ?? 'PDF';
                $ciData   = $this->response->ShipmentResults->Form->Image->GraphicImage;
                //$filename = Yii::getAlias('@backend') . "/web/ci/CI{$this->shipment->id}";
                //$filePath = Shipment::createFile($filename, $ciFormat, $ciData);
            }

            $this->isShipped = true;
        }
        return $this;
    }
}
