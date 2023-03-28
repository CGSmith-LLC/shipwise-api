<?php

namespace console\jobs\platforms\shopify;

use common\models\{Country,
    Order,
    State,
    Status,
    EcommerceIntegration,
    EcommerceOrderLog,
    EcommercePlatform};
use common\services\platforms\CreateOrderService;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use yii\web\NotFoundHttpException;

/**
 * Class ParseShopifyOrderJob
 * @package console\jobs\platforms\shopify
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

        // Parse the order only if it doesn't exist in our table:
        if (!CreateOrderService::isOrderExists([
            'origin' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'uuid' => (string)$this->rawOrder['id'],
            'customer_id' => $this->ecommerceIntegration->customer_id,
        ])) {
            $parsingErrors = $this->getParsingErrors();

            if (!$parsingErrors) {
                $this->parseOrderData();
                $this->parseAddressData();
                $this->parseItemsData();
                $order = $this->saveOrder();

                if ($order) {
                    EcommerceOrderLog::success($this->ecommerceIntegration, $this->rawOrder, $order);
                }
            } else {
                EcommerceOrderLog::failed($this->ecommerceIntegration, $this->rawOrder, ['errors' => $parsingErrors]);
            }
        }
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

    protected function getParsingErrors(): array
    {
        $errors = [];

        if (!isset($this->rawOrder['shipping_address'])) {
            $errors[] = 'Shipping address is missed.';
        }

        if (!isset($this->rawOrder['customer'])) {
            $errors[] = 'Customer is missed.';
        }

        return $errors;
    }

    protected function parseOrderData(): void
    {
        $this->parsedOrderAttributes = [
            'customer_id' => $this->ecommerceIntegration->customer_id,
            'customer_reference' => str_replace('#', '', (string)$this->rawOrder['name']),
            'status_id' => Status::OPEN,
            'uuid' => (string)$this->rawOrder['id'],
            'created_date' => (new \DateTime($this->rawOrder['created_at']))->format('Y-m-d'),
            'origin' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'notes' => trim($this->rawOrder['note']),
            'address_id' => 0, // To skip validation, will be overwritten in `CreateOrderService`
        ];
    }

    protected function parseAddressData(): void
    {
        $notProvided = 'Not provided';
        $name = $address1 = $address2 = $company = $city = $phone = $email = $zip = null;
        $stateId = 0;
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

        if (isset($this->rawOrder['contact_email'])) {
            $email = trim($this->rawOrder['contact_email']);
        }

        // Trying to find the phone number:
        if (isset($this->rawOrder['shipping_address']['phone'])) {
            $phone = trim($this->rawOrder['shipping_address']['phone']);
        } elseif (isset($this->rawOrder['phone']) && !empty($this->rawOrder['phone'])) {
            $phone = trim($this->rawOrder['phone']);
        } elseif (isset($this->rawOrder['customer']) && isset($this->rawOrder['customer']['phone']) && !empty($this->rawOrder['customer']['phone'])) {
            $phone = trim($this->rawOrder['customer']['phone']);
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
            'email' => $email,
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
                'sku' => ($item['sku']) ?: 'Not provided',
                'name' => $item['name'],
                'uuid' => (string)$item['id'],
            ];
        }
    }

    protected function saveOrder(): Order|bool
    {
        $createOrderService = new CreateOrderService($this->ecommerceIntegration->customer_id);
        $createOrderService->setOrder($this->parsedOrderAttributes);
        $createOrderService->setCarrier();
        $createOrderService->setAddress($this->parsedAddressAttributes);
        $createOrderService->setItems($this->parsedItemsAttributes);

        if ($createOrderService->isValid()) {
            return $createOrderService->create();
        }

        return false;
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
