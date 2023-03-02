<?php

namespace console\jobs\platforms;

use common\models\Country;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;
use common\models\State;
use common\models\Status;
use common\services\platforms\CreateOrderService;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use yii\web\NotFoundHttpException;

/**
 * Class ParseShopifyOrderJob
 * @package console\jobs\platforms
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/order
 */
class ParseShopifyOrderJob extends BaseObject implements RetryableJobInterface
{
    public array $rawOrder;
    public int $ecommerceIntegrationId;

    protected ?EcommerceIntegration $ecommerceIntegration = null;

    protected ?array $parsedOrderAttributes = [];
    protected ?array $parsedAddressAttributes = [];
    protected ?array $parsedItemsAttributes = [];

    /**
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        $this->setEcommerceIntegration();
        $this->parseOrderData();
        $this->parseAddressData();
        $this->parseItemsData();
        $this->saveOrder();
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setEcommerceIntegration(): void
    {
        $ecommerceIntegration = EcommerceIntegration::findOne($this->ecommerceIntegrationId);

        if (!$ecommerceIntegration) {
            throw new NotFoundHttpException('E-commerce integration not found.');
        }

        $this->ecommerceIntegration = $ecommerceIntegration;
    }

    protected function parseOrderData(): void
    {
        $this->parsedOrderAttributes = [
            'customer_id' => $this->ecommerceIntegration->customer_id,
            'customer_reference' => (string)$this->rawOrder['id'],
            'order_reference' => $this->rawOrder['name'],
            'status_id' => Status::OPEN,
            'uuid' => (string)$this->rawOrder['id'],
            'created_date' => (new \DateTime($this->rawOrder['created_at']))->format('Y-m-d'),
            'origin' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'notes' => $this->rawOrder['tags'],
            'address_id' => 0, // To skip validation, will be overwritten in `CreateOrderService`
        ];
    }

    protected function parseAddressData(): void
    {
        $notProvided = 'Not provided.';
        $name = null;
        $address1 = null;
        $address2 = null;
        $company = null;
        $city = null;
        $phone = null;
        $stateId = 0;
        $zip = null;
        $countryCode = State::DEFAULT_COUNTRY_ABBR;

        if (isset($this->rawOrder['shipping_address']['name'])) {
            $name = trim($this->rawOrder['shipping_address']['name']);
        }

        if (isset($this->rawOrder['shipping_address']['address1'])) {
            $address1 = trim($this->rawOrder['shipping_address']['address1']);
        }

        if (isset($this->rawOrder['shipping_address']['address2'])) {
            $address2 = trim($this->rawOrder['shipping_address']['address2']);
        }

        if (isset($this->rawOrder['shipping_address']['company'])) {
            $company = trim($this->rawOrder['shipping_address']['company']);
        }

        if (isset($this->rawOrder['shipping_address']['city'])) {
            $city = trim($this->rawOrder['shipping_address']['city']);
        }

        if (isset($this->rawOrder['shipping_address']['phone'])) {
            $phone = trim($this->rawOrder['shipping_address']['phone']);
        }

        if (isset($this->rawOrder['shipping_address']['zip'])) {
            $zip = trim($this->rawOrder['shipping_address']['zip']);
        }

        if (isset($this->rawOrder['shipping_address']['province_code'])) {
            $state = State::find()->where([
                'abbreviation' => trim($this->rawOrder['shipping_address']['province_code'])
            ])->one();

            if ($state) {
                $stateId = $state->id;
            }
        }

        if (isset($this->rawOrder['shipping_address']['country_code'])) {
            $country = Country::find()->where([
                'abbreviation' => trim($this->rawOrder['shipping_address']['country_code'])
            ])->one();

            if ($country) {
                $countryCode = $country->abbreviation;
            }
        }

        $this->parsedAddressAttributes = [
            'name' => ($name) ?: $notProvided,
            'address1' => ($address1) ?: $notProvided,
            'address2' => $address2,
            'company' => $company,
            'city' => ($city) ?: $notProvided,
            'phone' => ($phone) ?: $notProvided,
            'state_id' => $stateId,
            'zip' => ($zip) ?: $notProvided,
            'country' => $countryCode,
        ];
    }

    protected function parseItemsData(): void
    {
        foreach ($this->rawOrder['line_items'] as $item) {
            $this->parsedItemsAttributes[] = [
                'quantity' => $item['fulfillable_quantity'],
                'sku' => ($item['sku']) ?: 'Not provided.',
                'name' => $item['name'],
                'uuid' => (string)$item['id'],
            ];
        }
    }

    protected function saveOrder(): void
    {
        $createOrderService = new CreateOrderService($this->ecommerceIntegration->customer_id);
        $createOrderService->setOrder($this->parsedOrderAttributes);
        $createOrderService->setCarrier();
        $createOrderService->setAddress($this->parsedAddressAttributes);
        $createOrderService->setItems($this->parsedItemsAttributes);

        if ($createOrderService->isValid()) {
            $createOrderService->create();
        }
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }

    public function getTtr(): int
    {
        return 5 * 60;
    }
}
