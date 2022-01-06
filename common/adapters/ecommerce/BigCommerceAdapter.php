<?php

namespace common\adapters\ecommerce;

use common\exceptions\OrderExistsException;
use common\models\Address;
use common\models\forms\OrderForm;
use common\models\Order;
use common\models\State;
use common\models\Status;
use yii\base\Component;
use yii\console\Exception;

class BigCommerceAdapter extends Component
{
    const EVENT_BEFORE_PARSE = 'beforeParse';
    const EVENT_AFTER_PARSE = 'afterParse';

    public int $customer_id;

    /**
     * These statuses should update downstream (3pl) to cancel an order if needed
     *
     * See BigCommerceService::updateDownstream()
     */
    const STATUS_INCOMPLETE = 0;
    const STATUS_PENDING = 1;
    const STATUS_CANCELLED = 5;
    const STATUS_DECLINED = 6;
    const STATUS_AWAITING_PAYMENT = 7;
    const STATUS_MANUAL_VERIFICATION_REQUIRED = 12;
    const STATUS_DISPUTED = 13;

    /**
     * These statuses are not monitored but Shipwise will trigger a notification
     *
     * See BigCommerceService::notifyCustomer()
     */
    const STATUS_REFUNDED = 4;
    const STATUS_PARTIALLY_SHIPPED = 3;
    const STATUS_PARTIALLY_REFUNDED = 14;

    /**
     * These statuses are not monitored and Shipwise won't do anything about them
     */
    const STATUS_SHIPPED = 2;
    const STATUS_AWAITING_PICKUP = 8;
    const STATUS_AWAITING_SHIPMENT = 9;
    const STATUS_COMPLETED = 10;

    /**
     * This status is usually used (configurable) by Shipwise to send downstream (3pl)
     */
    const STATUS_AWAITING_FULFILLMENT = 11;

    /**
     * @param object $unparsedOrder
     * @throws Exception
     */
    public function parseOrder($unparsedOrder)
    {
        $model = new OrderForm();
        $model->order = new Order();
        $model->address = new Address();

        $this->trigger(self::EVENT_BEFORE_PARSE);

        // set order created date
        $createDate = isset($unparsedOrder['date_created']) ? new \DateTime($unparsedOrder['date_created']) : new \DateTime();

        $model->order->setAttributes([
            'customer_id' => $this->customer_id,
            'customer_reference' => (string) $unparsedOrder['id'],
            'status_id' => Status::OPEN,
            'uuid' => (string) $unparsedOrder['id'],
            'created_date' => $createDate->format('Y-m-d'),
            'origin' => 'BigCommerce',
            'address_id' => 0, // avoid validation issues
        ]);

        /** @var State $state */
        $state = State::findByAbbrOrName($unparsedOrder['shipping_addresses'][0]['country_iso2'], name: $unparsedOrder['shipping_addresses'][0]['state']);
        $model->address->setAttributes([
            'name' => $unparsedOrder['shipping_addresses'][0]['first_name'] . ' ' . $unparsedOrder['shipping_addresses'][0]['last_name'],
            'company' => $unparsedOrder['shipping_addresses'][0]['company'],
            'address1' => $unparsedOrder['shipping_addresses'][0]['street_1'],
            'address2' => $unparsedOrder['shipping_addresses'][0]['street_2'],
            'city' => $unparsedOrder['shipping_addresses'][0]['city'],
            'state_id' => $state->id,
            'country' => $state->country,
            'zip' => $unparsedOrder['shipping_addresses'][0]['zip'],
            'phone' => (!empty($unparsedOrder['shipping_addresses'][0]['phone'])) ? $unparsedOrder['shipping_addresses'][0]['phone'] : '555-555-5555',
        ]);

        foreach ($unparsedOrder['products'] as $unparsedProduct) {
            $items[] = [
                'quantity' => $unparsedProduct['quantity'],
                'sku' => $unparsedProduct['sku'],
                'name' => $unparsedProduct['name'],
            ];
        }
        $model->setItems($items);
        $model->validate();

        // @todo convert from a bigcommerce mapping
        //$order->setShipCarrier(FedEx::ID);
        //$order->setShipService(FedEx::SHIPWISE_GROUND_HOME);

        return $model;
    }

}
