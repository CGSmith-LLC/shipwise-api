<?php

namespace common\models\shipping\extension;

use common\models\Item;
use Yii;
use common\models\shipping\{
    Shipment,
    ShipmentPackage,
    ShipmentPlugin,
    ShipmentException
};
use yii\helpers\FileHelper;

/**
 * Class AmazonMWS
 *
 * Handles shipment creation using Amazon MWS Merchant Fulfillment
 *
 * @see     http://docs.developer.amazonservices.com/en_US/merch_fulfill/MerchFulfill_CreateShipment.html
 * @package common\models\shipping\extension
 *
 * @property int $maxRetries
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
     * @var int Max number of retries for API calls to Amazon MWS.
     *          Used in shipmentExecute method to handle throttling
     */
    protected $maxRetries = 30;

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
     * This function builds AmazonMWS shipment request
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
        $request = new \MWSMerchantFulfillmentService_Model_CreateShipmentRequest();
        $request->setSellerId($this->sellerId);
        $request->setMWSAuthToken($this->mwsAuthToken);
        $request->setShippingServiceId($this->shipment->service->carrier_code ?? null);

        $details = new \MWSMerchantFulfillmentService_Model_ShipmentRequestDetails();
        $details->setAmazonOrderId($this->shipment->reference1);

        /**
         * Amazon Items
         */
        $itemList = [];
        /** @var Item[] $orderItems */
        $orderItems = Item::find()->where(['order_id' => $this->shipment->order_id])->all();

        foreach ($orderItems as $orderItem) {
            if (isset($orderItem->uuid)) { // If the item doesn't have a UUID then we assume it is packaging
                $_item = new \MWSMerchantFulfillmentService_Model_Item();
                $_item->setOrderItemId($orderItem->uuid);
                if (!is_null($orderItem->alias_quantity)) {
                    $_item->setQuantity($orderItem->alias_quantity);
                }else {
                    $_item->setQuantity($orderItem->quantity);
                }
                $itemList[] = $_item;
            }
        }
        $details->setItemList($itemList);

        /**
         * Ship From
         */
        $shipFrom = new \MWSMerchantFulfillmentService_Model_Address();
        $shipFrom->setName(
            substr(
                $this->shipment->sender_company ?? $this->shipment->sender_contact,
                0,
                30
            )
        );
        $shipFrom->setAddressLine1(substr($this->shipment->sender_address1, 0, 180));
        if (!empty($this->shipment->sender_address2)) {
            $shipFrom->setAddressLine2(substr($this->shipment->sender_address2, 0, 60));
        }
        $shipFrom->setEmail($this->shipment->sender_email);
        $shipFrom->setCity(substr($this->shipment->sender_city, 0, 30));
        $shipFrom->setStateOrProvinceCode($this->shipment->sender_state);
        $shipFrom->setPostalCode($this->shipment->sender_postal_code);
        $shipFrom->setCountryCode($this->shipment->sender_country);
        $shipFrom->setPhone(preg_replace("/[^0-9]/", "", $this->shipment->sender_phone));
        $details->setShipFromAddress($shipFrom);

        /**
         * Package Dimensions and Weight
         * @todo To be determined how to handle MPS (multiple-piece-shipment) cases
         */
        $package = $this->shipment->getPackages()[0] ?? new ShipmentPackage();

        $dims = new \MWSMerchantFulfillmentService_Model_PackageDimensions();
        $dims->setLength(round($package->length, 2));
        $dims->setWidth(round($package->width, 2));
        $dims->setHeight(round($package->height, 2));
        $_unit = ($this->shipment->dim_units == Shipment::DIM_UNITS_IN) ? 'inches' : 'centimeters';
        $dims->setUnit($_unit);
        $details->setPackageDimensions($dims);

        $weight = new \MWSMerchantFulfillmentService_Model_Weight();
        if ($this->shipment->weight_units == Shipment::WEIGHT_UNITS_LB) {
            $_unit  = 'ounces';
            $_value = $package->weight * 16;
        } else {
            $_unit  = 'grams';
            $_value = $package->weight * 1000;
        }
        $weight->setValue(round($_value, 2));
        $weight->setUnit($_unit);
        $details->setWeight($weight);

        /**
         * Shipping Service Options
         */
        $options = new \MWSMerchantFulfillmentService_Model_ShippingServiceOptions();
        /**
         * DeliveryExperience values:
         * - DeliveryConfirmationWithAdultSignature - Delivery confirmation with adult signature.
         * - DeliveryConfirmationWithSignature - Delivery confirmation with signature. Required for DPD (UK).
         * - DeliveryConfirmationWithoutSignature - Delivery confirmation without signature.
         * - NoTracking - No delivery confirmation.
         */
        $options->setDeliveryExperience(
            'DeliveryConfirmationWithoutSignature'
        ); // @todo Please review this for your need

        /**
         * CarrierWillPickUp
         * Indicates whether the carrier will pick up the package.
         * Note: Scheduled carrier pickup is available only using Dynamex (US), DPD (UK), and Royal Mail (UK).
         */
        $options->setCarrierWillPickUp(false);

        /**
         * LabelFormat
         * The seller's preferred label format.
         * Note: Not all LabelFormat values are supported by all carriers.
         * Specifying a LabelFormat value that is not supported by a carrier will filter out shipping service offers
         * from that carrier.
         * Must match one of the AvailableLabelFormats returned by GetEligibleShippingServices operation.
         */
        $options->setLabelFormat('PNG'); // 4x6 PNG Default
        $details->setShippingServiceOptions($options);

        /**
         * Label Customization
         * The type of standard identifier to print on the label. StandardIdForLabel values: AmazonOrderId.
         */
        $labelCustomization = new \MWSMerchantFulfillmentService_Model_LabelCustomization();
        $labelCustomization->setStandardIdForLabel('AmazonOrderId');
        $details->setLabelCustomization($labelCustomization);

        $request->setShipmentRequestDetails($details);

        $this->data = $request;

        Yii::debug($this->data, self::getPluginName() . ' request');

        return $this;
    }

    /**
     * Execute the API Request
     *
     * @param int $retryNb Retry # increment
     *
     * @return $this
     * @throws ShipmentException
     * @version 2020.04.15
     *
     * Handles throttling. Ref: http://docs.developer.amazonservices.com/en_US/dev_guide/DG_Throttling.html
     * Amazon MWS CreateShipment operation has a maximum request quota of 10 and a restore rate of five requests every
     * second. (Error code returned: 'RequestThrottled' (HTTP 503))
     */
    protected function shipmentExecute($retryNb = 0)
    {
        $config = [
            'ServiceURL'    => $this->urlProd,
            'ProxyHost'     => null,
            'ProxyPort'     => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        ];

        $service = new \MWSMerchantFulfillmentService_Client(
            $this->awsAccessKeyId,
            $this->awsSecretKey,
            Yii::$app->name,
            '1.0',
            $config
        );

        /************************************************************************
         * Uncomment to try out Mock Service that simulates MWSMerchantFulfillmentService
         * responses without calling MWSMerchantFulfillmentService service.
         *
         * Responses are loaded from local XML files. You can tweak XML files to
         * experiment with various outputs during development
         *
         * XML files available under MWSMerchantFulfillmentService/Mock tree
         ***********************************************************************/
        //$service = new \MWSMerchantFulfillmentService_Mock();

        try {
            $this->response = $service->CreateShipment($this->data);
            Yii::debug($this->response, self::getPluginName() . ' response');
        } catch (\MWSMerchantFulfillmentService_Exception $ex) {
            Yii::debug($ex, 'Amazon MWS Exception');

            // handle throttling
            if (($ex->getErrorCode() == 'RequestThrottled') && ($retryNb < $this->maxRetries)) {
                sleep(1);
                return $this->shipmentExecute(++$retryNb);
            }

            $msg = "Caught Exception: " . $ex->getMessage() . "\n";
            $msg .= "Response Status Code: " . $ex->getStatusCode() . "\n";
            $msg .= "Error Code: " . $ex->getErrorCode() . "\n";
            $msg .= "Error Type: " . $ex->getErrorType() . "\n";
            $msg .= "Request ID: " . $ex->getRequestId() . "\n";
            //$msg .= "XML: " . $ex->getXML() . "\n");
            Yii::debug($ex->getXML(), 'Amazon MWS Exception');
            $msg .= "ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n";

            throw new ShipmentException($msg);
        }

        return $this;
    }

    /**
     * Shipment process
     *
     * This method will process the response received from carrier API
     *
     * @return $this
     * @throws ShipmentException
     * @version 2020.04.15
     *
     */
    protected function shipmentProcess()
    {
        /** @var \MWSMerchantFulfillmentService_Model_CreateShipmentResponse $response */
        $response = $this->response;

        /** @var \MWSMerchantFulfillmentService_Model_CreateShipmentResult $result */
        $result = $response->getCreateShipmentResult() ?? null;

        if (!$result instanceof \MWSMerchantFulfillmentService_Model_CreateShipmentResult) {
            Yii::debug($this->response);
            throw new ShipmentException('Incorrect response');
        }

        /** @var \MWSMerchantFulfillmentService_Model_Shipment $shipment */
        $shipment = $result->getShipment() ?? null;

        /**
         * Shipment tracking identifier provided by the carrier
         * @var string $trackingId
         */
        $trackingId = $shipment->getTrackingId() ?? null;

        /** @var \MWSMerchantFulfillmentService_Model_Label $label */
        $label = $shipment->getLabel() ?? null;

        if ($shipment->getStatus() != 'Purchased') {
            $msg = 'Incorrect shipment status: ' . $shipment->getStatus();
            Yii::debug($this->response);
            throw new ShipmentException($msg);
        }

        /**
         * SUCCESS Case
         *
         * Retrieve tracking and label details from completed shipment reply.
         */

        /**
         * @var ShipmentPackage $package
         */
        $package = &$this->shipment->getPackages()[0];

        // Tracking details
        $package->tracking_num        = $trackingId;
        $package->master_tracking_num = $trackingId;
        //$package->tracking_url        = "{$this->trackingURL}?tracknum=" . $package->tracking_num;

        // Amazon-defined shipment identifier
        $this->shipment->external_id1 = $shipment->getShipmentId();

        /** @var \MWSMerchantFulfillmentService_Model_FileContents $fileContents */
        $fileContents = $label->getFileContents();

        // Label data
        // According to Amazon MWS docs: "Base64-encoded data for printing labels, GZip-compressed string"
        $package->label_data   = $fileContents->getContents();
        $package->label_format = strtoupper($label->getLabelFormat());

        // Convert to PDF (using ImageMagick)
        $dir = Yii::getAlias('@frontend') . '/runtime/pdf/';
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0777, true);
        }
        $tempFilename = $dir . 'tmp' . $package->tracking_num . '_' . time() . '.' . strtolower($package->label_format);
        $fp           = fopen($tempFilename, 'wb');
        fwrite($fp, gzdecode(base64_decode($package->label_data)));
        fclose($fp);
        $newFilename = $dir . $package->tracking_num . '_' . time() . '.pdf';
        exec("convert {$tempFilename} {$newFilename}");
        @unlink($tempFilename);

        $this->shipment->mergedLabelsData   = base64_encode(file_get_contents($newFilename));
        $this->shipment->mergedLabelsFormat = $package->label_format = 'PDF';
        @unlink($newFilename);

        $this->isShipped = true;

        return $this;
    }
}
