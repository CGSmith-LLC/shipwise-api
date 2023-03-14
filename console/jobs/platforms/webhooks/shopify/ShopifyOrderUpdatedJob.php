<?php

namespace console\jobs\platforms\webhooks\shopify;

use common\models\{Address, Country, Item, Order, State};
use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use yii\helpers\Json;

/**
 * Class ShopifyOrderUpdatedJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-orders-updated
 */
class ShopifyOrderUpdatedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->update()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    protected function update(): bool
    {
        $payload = Json::decode($this->ecommerceWebhook->payload);

        if (isset($payload['id'])) {
            $internalOrder = $this->getOrderByExternalId((int)$payload['id']);

            if ($internalOrder) {
                $this->updateOrder($internalOrder, $payload);
                $this->updateAddress($internalOrder->address, $payload);
                $this->updateItems($internalOrder, $payload);

                return true;
            }
        }

        return false;
    }

    protected function updateOrder(Order $internalOrder, array $payload): void
    {
        $internalOrder->notes = trim($payload['tags']);
        $internalOrder->save();
    }

    protected function updateAddress(Address $internalAddress, array $payload): void
    {
        $notProvided = 'Not provided';
        $name = trim($payload['shipping_address']['name']);
        $address1 = trim($payload['shipping_address']['address1']);
        $address2 = trim($payload['shipping_address']['address2']);
        $company = trim($payload['shipping_address']['company']);
        $city = trim($payload['shipping_address']['city']);
        $email = trim($payload['contact_email']);
        $zip = trim($payload['shipping_address']['zip']);

        $internalAddress->name = ($name) ?: $notProvided;
        $internalAddress->address1 = ($address1) ?: $notProvided;
        $internalAddress->address2 = ($address2) ?: null;
        $internalAddress->company = ($company) ?: null;
        $internalAddress->city = ($city) ?: $notProvided;
        $internalAddress->email = ($email) ?: null;
        $internalAddress->phone = ($this->getPhoneNumberFromPayload($payload)) ?: $notProvided;
        $internalAddress->zip = ($zip) ?: $notProvided;
        $internalAddress->state_id = $this->getStateIdFromPayload($payload);
        $internalAddress->country = $this->getCountryCodeFromPayload($payload);
        $internalAddress->save();
    }

    protected function updateItems(Order $internalOrder, array $payload): void
    {
        $internalItems = $internalOrder->items;
        $internalUuids = $externalUuids = [];

        // Collect external item UUIDs:
        if (isset($payload['line_items'])) {
            foreach ($payload['line_items'] as $externalItem) {
                $externalUuids[] = (string)$externalItem['id'];
            }
        }

        // Collect internal item UUIDs:
        foreach ($internalItems as $internalItem) {
            $internalUuids[] = (string)$internalItem->uuid;
        }

        // Remove internal Items that aren't presented in the `line_items`:
        foreach ($internalItems as $internalItem) {
            if (!in_array($internalItem->uuid, $externalUuids)) {
                $internalItem->delete();
            }
        }

        // Add or update:
        foreach ($payload['line_items'] as $externalItem) {
            $externalItemUuid = (string)$externalItem['id'];

            $attributes = [
                'order_id' => $internalOrder->id,
                'quantity' => $externalItem['fulfillable_quantity'],
                'sku' => ($externalItem['sku']) ?: 'Not provided',
                'name' => $externalItem['name'],
                'uuid' => $externalItemUuid,
            ];

            if (!in_array($externalItemUuid, $internalUuids)) { // Add:
                $orderItem = new Item();
                $orderItem->setAttributes($attributes);
                $orderItem->save();
            } else { // Update:
                foreach ($internalItems as $internalItem) {
                    if ($internalItem->uuid == $externalItemUuid) {
                        $internalItem->setAttributes($attributes);
                        $internalItem->save();
                    }
                }
            }
        }
    }

    protected function getPhoneNumberFromPayload(array $payload): ?string
    {
        $phone = null;

        if (isset($payload['shipping_address']['phone'])) {
            $phone = trim($payload['shipping_address']['phone']);
        } elseif (isset($payload['phone']) && !empty($payload['phone'])) {
            $phone = trim($payload['phone']);
        } elseif (isset($payload['customer']) && isset($payload['customer']['phone']) && !empty($payload['customer']['phone'])) {
            $phone = trim($payload['customer']['phone']);
        }

        return $phone;
    }

    protected function getStateIdFromPayload(array $payload): int
    {
        $stateId = 0;

        if (isset($payload['shipping_address']['province_code'])) {
            $state = State::find()->where([
                'abbreviation' => trim($payload['shipping_address']['province_code'])
            ])->one();

            if ($state) {
                $stateId = (int)$state->id;
            }
        }

        return $stateId;
    }

    protected function getCountryCodeFromPayload(array $payload): string
    {
        $countryCode = State::DEFAULT_COUNTRY_ABBR;

        if (isset($payload['shipping_address']['country_code'])) {
            $country = Country::find()->where([
                'abbreviation' => trim($payload['shipping_address']['country_code'])
            ])->one();

            if ($country) {
                $countryCode = $country->abbreviation;
            }
        }

        return $countryCode;
    }
}
