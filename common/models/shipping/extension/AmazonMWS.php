<?php

namespace common\models\shipping\extension;

use common\models\{Charge, Money};
use Yii;
use common\models\shipping\{
    Carrier,
    PackageType,
    Service,
    ShipmentPlugin,
    ShipmentException
};

/**
 * Class AmazonMWS
 *
 * Handles shipment creation using Amazon MWS Merchant Fulfillment
 *
 * @see     http://docs.developer.amazonservices.com/en_US/merch_fulfill/MerchFulfill_CreateShipment.html
 * @package common\models\shipping\extension
 */
class AmazonMWS extends ShipmentPlugin
{

    /**
     * Development API url
     *
     * @var string
     */
    private $urlDev = 'n/a';

    /**
     * Production API url
     *
     * This is for North America region
     * @todo add other regions
     *
     * @var string
     */
    private $urlProd = 'https://mws.amazonservices.com/MerchantFulfillment/2015-06-01';

    /**
     * Marketplace ID
     *
     * This is for North America region
     * @todo add other regions
     *
     * @var string
     */
    private $marketplaceId = 'ATVPDKIKX0DER';

    /**
     * Base Tracking URL
     * @todo To be defined if Amazon MWS provides a tracking URL
     *
     * @var string
     */
    protected $trackingURL = "n/a";

    /**
     * Connection Data
     *
     * @var string
     */
    private $sellerId;
    private $mwsAuthToken;
    private $awsAccessKeyId;
    private $awsSecretKey;

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
     * @todo To be defined if Amazon MWS provides a tracking URL
     *
     * @var bool
     */
    public $isTrackable = true;

    /** @inheritdoc */
    public function autoload($customerId = null)
    {
        $this->setAccountInfo(
            Yii::$app->customerSettings->get('amazon_mws_seller_id', $customerId),
            Yii::$app->customerSettings->get('amazon_mws_auth_token', $customerId),
            Yii::$app->customerSettings->get('amazon_mws_aws_access_key_id', $customerId),
            Yii::$app->customerSettings->get('amazon_mws_aws_secret_key', $customerId)
        );
    }

    /**
     * Set FedEx Account Info
     *
     * @param string $sellerId       Amazon MWS Seller Id
     * @param string $mwsAuthToken   Amazon MWS Auth Token
     * @param string $awsAccessKeyId Amazon MWS AWS Access Key Id
     * @param string $awsSecretKey   Amazon MWS AWS Secret Key
     *
     * @return $this
     */
    public function setAccountInfo(
        $sellerId,
        $mwsAuthToken,
        $awsAccessKeyId,
        $awsSecretKey
    ) {
        $this->sellerId       = $sellerId;
        $this->mwsAuthToken   = $mwsAuthToken;
        $this->awsAccessKeyId = $awsAccessKeyId;
        $this->awsSecretKey   = $awsSecretKey;

        return $this;
    }

    /**
     * Get Plugin Name
     *
     * @return string
     */
    public function getPluginName()
    {
        return static::PLUGIN_NAME;
    }


    protected function ratePrepare()
    {
    }

    protected function rateExecute()
    {
    }

    protected function rateProcess()
    {
    }

    /**
     * Prepare Shipment Call to carrier API
     *
     * This function builds UPS shipment request
     *
     * @return $this
     * @throws \Exception
     * @version 2020.04.15
     */
    protected function shipmentPrepare()
    {
        // Determine if MPS (Multiple Piece Shipment)
        $this->isMps = (count($this->shipment->getPackages()) > 1);

        /**
         * Build Amazon MWS `CreateShipmentRequest`
         */

        /************************************************************************
         * Uncomment to try out Mock Service that simulates MWSMerchantFulfillmentService
         * responses without calling MWSMerchantFulfillmentService service.
         *
         * Responses are loaded from local XML files. You can tweak XML files to
         * experiment with various outputs during development
         *
         * XML files available under MWSMerchantFulfillmentService/Mock tree
         *
         ***********************************************************************/
        $service = new \MWSMerchantFulfillmentService_Mock();

        $request = new \MWSMerchantFulfillmentService_Model_CreateShipmentRequest();
        $request->setSellerId('123456');

        \yii\helpers\VarDumper::dump($request, 10, true);
        exit;

        return $this;
    }

    /**
     * Execute the API Request
     *
     * @return $this
     * @throws ShipmentException
     * @version 2020.04.15
     */
    protected function shipmentExecute()
    {

        return $this;
    }

    /**
     * Shipment process
     *
     * This method will process the response received from carrier API
     *
     * @return $this
     * @version 2020.04.15
     *
     */
    protected function shipmentProcess()
    {

        return $this;
    }
}
