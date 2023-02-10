<?php

namespace common\models\shipping\extension;

use Yii;
use SellingPartnerApi\Api\ShippingV2Api;
use SellingPartnerApi\Model\ShippingV2\DirectPurchaseRequest;
use SellingPartnerApi\Model\ShippingV2\{
    Address,
    Package,
    Dimensions,
    Weight,
    Item as ApiItem,
    ChannelDetails
};
use common\models\Item;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Endpoint;
use common\models\shipping\{
    Shipment,
    ShipmentPackage,
    ShipmentPlugin,
    ShipmentException
};

/**
 * Class AmazonSellingPartner
 *
 * @package common\models\shipping\extension
 *
 * @see https://developer-docs.amazon.com/amazon-shipping/docs/amazon-shipping-api-v2-use-case-guide
 * @see https://developer-docs.amazon.com/amazon-shipping/docs/shipping-api-v2-reference
 * @see https://github.com/jlevers/selling-partner-api
 * @see https://github.com/jlevers/selling-partner-api/blob/main/docs/Api/ShippingV2Api.md
 */
class AmazonSellingPartner extends ShipmentPlugin
{
    protected ?int $customerId;
    protected ShippingV2Api $apiInstance;
    protected array $requestBody;

    /**
     * @throws ShipmentException
     * @throws \Exception
     */
    public function autoload($customerId = null): void
    {
        $this->customerId = $customerId;
        $refreshToken = Yii::$app->customerSettings->get('amazon_spapi_refresh_token', $this->customerId);

        if (!$refreshToken) {
            throw new ShipmentException("No Amazon SPAPI refresh token provided for customer.");
        }

        $config = new Configuration([
            "lwaClientId" => Yii::$app->params['amazon-lwaClient'],
            "lwaClientSecret" => Yii::$app->params['amazon-lwaSecret'],
            "lwaRefreshToken" => $refreshToken,
            "awsAccessKeyId" => Yii::$app->params['amazon-accessKey'],
            "awsSecretAccessKey" => Yii::$app->params['amazon-secretKey'],
            "endpoint" => Endpoint::NA
        ]);

        $this->apiInstance = new ShippingV2Api($config);
    }

    public function getPluginName(): string
    {
        return static::PLUGIN_NAME;
    }

    protected function shipmentPrepare(): static
    {
        $data = [
            // 'ship_to' => '',
            'ship_from' => $this->getShipFrom(),
            // 'return_to' => '',
            'packages' => $this->getPackages(),
            'channel_details' => $this->getChannelDetails(),
            // 'label_specifications' => '',
        ];

        $body = new DirectPurchaseRequest($data);

        /**
         * string | A unique value which the server uses to recognize subsequent retries of the same request.
         */
        $xAmznIdempotencyKey = $this->customerId . '-' . $this->shipment->order_id;

        /**
         * string | The IETF Language Tag (i.e. en-US, fr-CA).
         */
        $locale = 'en-US';

        /**
         * string | Amazon shipping business to assume for this request. The default is AmazonShipping_UK.
         * @see https://developer-docs.amazon.com/amazon-shipping/docs/shipping-api-v2-reference#chargetype
         */
        $xAmznShippingBusinessId = 'AmazonShipping_US';

        $this->requestBody = [
            'body' => $body,
            'xAmznIdempotencyKey' => $xAmznIdempotencyKey,
            'locale' => $locale,
            'xAmznShippingBusinessId' => $xAmznShippingBusinessId
        ];

        return $this;
    }

    /**
     * @throws \SellingPartnerApi\ApiException
     * @throws ShipmentException
     */
    protected function shipmentExecute(): static
    {
        try {
            $result = $this->apiInstance->directPurchaseShipment(
                $this->requestBody['body'],
                $this->requestBody['xAmznIdempotencyKey'],
                $this->requestBody['locale'],
                $this->requestBody['xAmznShippingBusinessId']);

            print_r($result);
        } catch (\Exception $e) {
            throw new ShipmentException('Exception when calling ShippingV2Api->directPurchaseShipment: ' . $e->getMessage());
        }

        exit;
        return $this;
    }

    protected function getShipFrom(): Address
    {
        $shipFrom = new Address();
        $shipFrom->setName($this->shipment->sender_company ?? $this->shipment->sender_contact);
        $shipFrom->setAddressLine1($this->shipment->sender_address1);
        if (!empty($this->shipment->sender_address2)) {
            $shipFrom->setAddressLine2($this->shipment->sender_address2);
        }
        $shipFrom->setEmail($this->shipment->sender_email);
        $shipFrom->setCity($this->shipment->sender_city);
        $shipFrom->setStateOrRegion($this->shipment->sender_state);
        $shipFrom->setPostalCode($this->shipment->sender_postal_code);
        $shipFrom->setCountryCode($this->shipment->sender_country);
        $shipFrom->setPhoneNumber(preg_replace("/[^0-9]/", "", $this->shipment->sender_phone));

        return $shipFrom;
    }

    protected function getPackages(): array
    {
        $packages = $this->shipment->getPackages();
        $data = [];

        if ($packages) {
            foreach ($packages as $package) {
                $apiItems = [];
                $orderItems = Item::find()
                    ->where(['order_id' => $this->shipment->order_id])
                    ->all();

                foreach ($orderItems as $orderItem) {
                    // If the item doesn't have a UUID then we assume it is packaging:
                    if (isset($orderItem->uuid)) {
                        $apiItems[] = new ApiItem([
                            'item_identifier' => $orderItem->uuid,
                            'quantity' => (!is_null($orderItem->alias_quantity))
                                ? $orderItem->alias_quantity
                                : $orderItem->quantity
                        ]);
                    }
                }

                $data[] = new Package([
                    'dimensions' => new Dimensions([
                        'length' => round($package->length, 2),
                        'width' => round($package->width, 2),
                        'height' => round($package->height, 2),
                        'unit' => ($this->shipment->dim_units == Shipment::DIM_UNITS_IN)
                            ? Dimensions::UNIT_INCH
                            : Dimensions::UNIT_CENTIMETER
                    ]),
                    'weight' => new Weight([
                        'unit' => ($this->shipment->weight_units == Shipment::WEIGHT_UNITS_LB)
                            ? Weight::UNIT_OUNCE
                            : Weight::UNIT_GRAM,
                        'value' => round(($this->shipment->weight_units == Shipment::WEIGHT_UNITS_LB)
                            ? $package->weight * 16
                            : $package->weight * 1000, 2)
                    ]),
                    'insured_value' => 'getInsuredValue',
                    'package_client_reference_id' => $package->reference1,
                    'items' => $apiItems
                ]);
            }
        }

        return $data;
    }

    protected function getChannelDetails(): ChannelDetails
    {
        return new ChannelDetails([
            'channel_type' => ChannelDetails::CHANNEL_TYPE_AMAZON
        ]);
    }

    protected function shipmentProcess(): static
    {
        return $this;
    }

    protected function ratePrepare()
    {
        // TODO: Implement ratePrepare() method.
    }

    protected function rateExecute()
    {
        // TODO: Implement rateExecute() method.
    }

    protected function rateProcess()
    {
        // TODO: Implement rateProcess() method.
    }
}
