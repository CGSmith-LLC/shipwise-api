<?php

namespace common\models\shipping\extension;

use common\models\{Charge, Money};
use Yii;
use common\models\shipping\{
    Carrier,
    PackageType,
    Service,
    ShipmentPlugin,
    ShipmentException,
    ShipmentRate};

/**
 * Class FedExPlugin
 *
 * @package common\models\shipping\extension
 */
class FedExPlugin extends ShipmentPlugin
{

    /**
     * Plugin Name
     *
     * @var string Constant
     */
    final const PLUGIN_NAME = "FedEx";

    /**
     * Development API url
     *
     * @var string
     */
    private string $urlDev = 'https://wsbeta.fedex.com:443/web-services/';

    /**
     * Production API url
     *
     * @var string
     */
    private string $urlProd = 'https://ws.fedex.com:443/web-services/';

    /**
     * Base Tracking URL
     *
     * @var string
     */
    protected $trackingURL = "https://www.fedex.com/apps/fedextrack/";

    /**
     * Connection Data
     *
     * @var string
     */
    private $key;
    private $password;
    private $accountNumber;
    private $meterNumber;

    /**
     * Type of Drop off, default value "REGULAR_PICKUP"
     *
     * valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
     *
     * @var string
     */
    private string $dropOffType = "REGULAR_PICKUP";

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
     * Master tracking ID
     *
     * @see wsdl/fedex/FedExShipService_v21.wsdl
     * @var \stdClass
     */
    protected $masterTrackingId;

    /**
     * Whether the shipment plugin has tracking API (non URL)
     *
     * @var bool
     */
    public $isTrackable = true;

    /** @inheritdoc */
    public function autoload($customerId = null)
    {
        $this->isProduction = !(bool)(int)Yii::$app->customerSettings->get('fedex_api_test_mode', $customerId);

        $this->setAccountInfo(
            $this->isProduction
                ? Yii::$app->customerSettings->get('fedex_api_key', $customerId)
                : Yii::$app->customerSettings->get('fedex_api_key_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('fedex_api_password', $customerId)
                : Yii::$app->customerSettings->get('fedex_api_password_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('fedex_api_account', $customerId)
                : Yii::$app->customerSettings->get('fedex_api_account_test', $customerId),

            $this->isProduction
                ? Yii::$app->customerSettings->get('fedex_api_meter', $customerId)
                : Yii::$app->customerSettings->get('fedex_api_meter_test', $customerId)
        );
    }

    /**
     * Set FedEx Account Info
     *
     * @param string $key           FedEx Key
     * @param string $password      FedEx Password
     * @param string $accountNumber FedEx Account Number
     * @param string $meterNumber   FedEx Meter Number
     *
     * @return $this
     */
    public function setAccountInfo(
        $key,
        $password,
        $accountNumber,
        $meterNumber
    ) {
        $this->key           = $key;
        $this->password      = $password;
        $this->accountNumber = $accountNumber;
        $this->meterNumber   = $meterNumber;

        return $this;
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
     * @throws \Exception
     * @version 2019.12.20
     */
    protected function ratePrepare()
    {

        /**
         * Build FedEx `RateRequest`
         */
        $this->data = [

            'WebAuthenticationDetail' => [
                'ParentCredential' => [
                    'Key'      => $this->key,
                    'Password' => $this->password,
                ],
                'UserCredential'   => [
                    'Key'      => $this->key,
                    'Password' => $this->password,
                ],
            ],

            'ClientDetail' => [
                'AccountNumber' => $this->accountNumber,
                'MeterNumber'   => $this->meterNumber,
            ],

            'TransactionDetail' => [
                'CustomerTransactionId' => 'RateRequest_v24',
            ],

            'Version' => [
                'ServiceId'    => 'crs',
                'Major'        => '24',
                'Intermediate' => '0',
                'Minor'        => '0',
            ],

            'ReturnTransitAndCommit' => true,

            'RequestedShipment' => [

                'DropoffType'   => $this->dropOffType,
                'ShipTimestamp' => (new \DateTime($this->shipment->shipment_date))->format('c'),
                'PackagingType' => PackageType::map(Carrier::FEDEX, $this->shipment->package_type) ?? null,
                //'RateRequestTypes' => ['NONE'],

                'Shipper' => [

                    'Contact' => [
                        'PersonName'  => $this->shipment->sender_contact,
                        'CompanyName' => $this->shipment->sender_company,
                        'PhoneNumber' => $this->shipment->sender_phone,
                    ],
                    'Address' => [
                        'StreetLines'         => [
                            $this->shipment->sender_address1,
                            $this->shipment->sender_address2 ?? null,
                        ],
                        'City'                => $this->shipment->sender_city,
                        'StateOrProvinceCode' => $this->shipment->sender_state,
                        'PostalCode'          => $this->shipment->sender_postal_code,
                        'CountryCode'         => $this->shipment->sender_country,
                    ],
                ],

                'Recipient' => [
                    'Contact' => [
                        'PersonName'  => $this->shipment->recipient_contact,
                        'CompanyName' => $this->shipment->recipient_company,
                        'PhoneNumber' => $this->shipment->recipient_phone,
                    ],
                    'Address' => [
                        'StreetLines'         => [
                            $this->shipment->recipient_address1,
                            $this->shipment->recipient_address2 ?? null,
                        ],
                        'City'                => $this->shipment->recipient_city,
                        'StateOrProvinceCode' => $this->shipment->recipient_state,
                        'PostalCode'          => $this->shipment->recipient_postal_code,
                        'CountryCode'         => $this->shipment->recipient_country,
                        'Residential'         => (bool)$this->shipment->recipient_is_residential,
                    ],
                ],

                'ShippingChargesPayment' => [
                    'PaymentType' => $this->shipment->bill_transport_to,
                    'Payor'       => [
                        'ResponsibleParty' => [
                            'AccountNumber' => $this->shipment->bill_transport_account_num ?? $this->accountNumber,
                            'Contact'       => null,
                            'Address'       => [
                                'CountryCode' => $this->shipment->sender_country,
                            ],
                        ],
                    ],
                ],

                'PackageCount' => count($this->shipment->getPackages()),
            ],
        ];

        /**
         * Rates comparison or specific service
         */
        if ($this->shipment->service) {
            $this->data['RequestedShipment']['ServiceType'] = $this->shipment->service->carrier_code ?? null;
        }

        /**
         * Insured value
         */
        if ($this->shipment->insurance_amount > 0) {
            $this->data['RequestedShipment']['TotalInsuredValue'] = [
                'Amount'   => $this->shipment->insurance_amount,
                'Currency' => $this->shipment->currency,
            ];
        }

        /**
         * Special services
         */
        $specialServiceTypes = [];
        if ($this->shipment->is_flat_rate) {
            $specialServiceTypes[] = 'FEDEX_ONE_RATE';
        }
        // Add here other special services... signature, dry ice, dg, etc.

        if (count($specialServiceTypes) > 0) {
            foreach ($specialServiceTypes as $specialServiceType) {
                $this->data['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = $specialServiceType;
            }
        }

        /**
         * Shipment packages
         */
        $sequenceNumber = 0;
        foreach ($this->shipment->getPackages() as $package) {

            $this->data['RequestedShipment']['RequestedPackageLineItems'][] = [

                'SequenceNumber'    => ++$sequenceNumber,
                'GroupPackageCount' => 1,
                'Weight'            => [
                    'Value' => round($package->weight, 2),
                    'Units' => $this->shipment->weight_units,
                ],
                'Dimensions'        => [
                    'Length' => round($package->length, 2),
                    'Width'  => round($package->width, 2),
                    'Height' => round($package->height, 2),
                    'Units'  => $this->shipment->dim_units,
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
     * @version 2019.12.20
     */
    protected function rateExecute()
    {
        $url = ($this->isProduction)
            ? $this->urlProd
            : $this->urlDev;

        $this->response = $this->runPostCall(
            "{$url}rate",
            $this->data,
            'getRates',
            __DIR__ . '/wsdl/fedex/RateService_v24.wsdl'
        );

        Yii::debug($this->data, 'FedEx API Request');
        Yii::debug($this->response, 'FedEx API Response');

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
     * @version 2019.12.20
     */
    protected function rateProcess()
    {
        if (!isset($this->response->HighestSeverity)) {
            return $this;
        }

        /**
         * FAILURE/ERROR Case
         *
         * This status means that our `shipment` request failed.
         * Retrieve error messages and throw exception.
         */
        if (in_array($this->response->HighestSeverity, ['FAILURE', 'ERROR'])) {

            // Object case
            if (isset($this->response->Notifications->Message)) {
                throw new ShipmentException(
                    $this->response->Notifications->Message .
                    " [{$this->response->Notifications->Code}]"
                );

                // Array of objects case
            } else {
                if (is_array($this->response->Notifications) &&
                    count($this->response->Notifications) > 0
                ) {
                    foreach ($this->response->Notifications as $notification) {
                        if (isset($notification->Message)) {
                            throw new ShipmentException(
                                $notification->Message .
                                " [{$notification->Code}]"
                            );
                        }
                    }
                }
            }
        }

        /**
         * NOTE/WARNING Case
         *
         * This status means that the shipment went through but FedEx has some
         * relevant notes to communicate to us. Retrieve notification messages
         * and continue.
         */
        if (in_array($this->response->HighestSeverity, ['NOTE', 'WARNING'])) {
            $notifications = [];
            if (isset($this->response->Notifications->Message)) { // one object
                $notifications[] = $this->response->Notifications;
            } else { // array of objects
                if (is_array($this->response->Notifications) && count($this->response->Notifications) > 0) {
                    $notifications = (array)$this->response->Notifications;
                }
            }
            foreach ($notifications as $notification) {
                $this->addWarning(@$notification->Message . " [" . @$notification->Code . "]");
            }
        }

        /**
         * SUCCESS Case
         *
         * This status means that our rate request was successful.
         * Retrieve available rates and add them to Shipment::_rates
         */
        if (!in_array($this->response->HighestSeverity, ['FAILURE', 'ERROR'])
            && isset($this->response->RateReplyDetails)) {

            /** @var array $rates Rate object(s) returned by API */
            $rates = [];
            if (isset($this->response->RateReplyDetails->ServiceType)) { // one object returned
                $rates[] = $this->response->RateReplyDetails;
            } else {
                // array of objects
                if (is_array($this->response->RateReplyDetails) && count($this->response->RateReplyDetails) > 0) {
                    $rates = $this->response->RateReplyDetails;
                }
            }

            foreach ((array)$rates as $rate) {
                if (isset($rate->ServiceType) && isset($rate->RatedShipmentDetails)) {

                    $service = Service::find()
                        ->forCarrierService($this->shipment->carrier->id, $rate->ServiceType)
                        ->one();

                    $_rate = new ShipmentRate();

                    $_rate->infoMessage = $this->getWarnings()[0] ?? null;

                    $_rate->serviceCode = $service->shipwise_code ?? $rate->ServiceType ?? null;
                    $_rate->serviceName = $service->name ?? $rate->ServiceDescription->Description ?? null;

                    $_rate->totalPrice = new Money([
                        'amount'   => $rate->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount ?? null,
                        'currency' => $rate->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Currency ?? null,
                    ]);

                    $_rate->addCharge(new Charge([
                        'type'        => 'BASE',
                        'description' => 'Base price',
                        'amount'      => new Money([
                            'amount'   => $rate->RatedShipmentDetails->ShipmentRateDetail->TotalBaseCharge->Amount ?? null,
                            'currency' => $rate->RatedShipmentDetails->ShipmentRateDetail->TotalBaseCharge->Currency ?? null,
                        ]),
                    ]));

                    $surcharges = [];
                    if (isset($rate->RatedShipmentDetails->ShipmentRateDetail->Surcharges->SurchargeType)) { // one object returned
                        $surcharges[] = $rate->RatedShipmentDetails->ShipmentRateDetail->Surcharges;
                    } else {
                        // array of objects
                        if (is_array($rate->RatedShipmentDetails->ShipmentRateDetail->Surcharges)
                            && count($rate->RatedShipmentDetails->ShipmentRateDetail->Surcharges) > 0) {
                            $surcharges = $rate->RatedShipmentDetails->ShipmentRateDetail->Surcharges;
                        }
                    }
                    foreach ($surcharges as $surcharge) {
                        $_rate->addCharge(new Charge([
                            'type'        => $surcharge->SurchargeType ?? null,
                            'description' => $surcharge->Description ?? null,
                            'amount'      => new Money([
                                'amount'   => $surcharge->Amount->Amount ?? null,
                                'currency' => $surcharge->Amount->Currency ?? null,
                            ]),
                        ]));
                    }

                    $_rate->deliveryTimeStamp = $rate->CommitDetails->CommitTimestamp ?? null;
                    $_rate->deliveryDayOfWeek = $rate->CommitDetails->DayOfWeek ?? null;
                    $_rate->transitTime       = $rate->CommitDetails->TransitTime ?? null;
                    $_rate->deliveryByTime    = null; // n/a for FedEx

                    $this->shipment->addRate($_rate);
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
     */
    public function runPostCall(
        $url,
        $data = null,
        $operationName = null,
        $wsdl = null
    ) {
        ini_set('soap.wsdl_cache_enabled', '0');

        // Make sure we have soap installed
        if (!extension_loaded('soap')) {
            throw new \LogicException('Soap is not installed');
        }

        /**
         * @var \SoapClient $soapClient
         */
        $soapClient = new \SoapClient(
            $wsdl,
            [
                'trace'              => !$this->isProduction,
                'connection_timeout' => $this->curlDownloadTimeoutInSeconds,
            ]
        );

        try {
            // Set the endpoint
            $soapClient->__setLocation($url);

            // Invoke FedEx Web Service operation
            $response = $soapClient->{$operationName}($data);

        } catch (\SoapFault $e) {

            // All errors caught here is when your soap request is not built
            // correctly this is the wsdl validations against its structure,
            // and not API returned errors.
            throw new ShipmentException(
                $e->faultstring .
                " [{$e->faultcode}]"
            );
        }

        return $response;
    }

    // @todo
    protected function shipmentPrepare(): never
    {
        throw new \Exception('Method not implemented');
    }

    // @todo
    protected function shipmentExecute(): never
    {
        throw new \Exception('Method not implemented');
    }

    // @todo
    protected function shipmentProcess(): never
    {
        throw new \Exception('Method not implemented');
    }
}
