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
     * @param object $unparsedOrder
     * @throws Exception
     */
    public function parseOrder($unparsedOrder)
    {
        $model = new OrderForm();
        $model->order = new Order();
        $model->address = new Address();

        $this->trigger(self::EVENT_BEFORE_PARSE);

        // check if order exists
        if (Order::find()
            ->where(['customer_reference' => (string) $unparsedOrder['id']])
            ->andWhere(['customer_id' => $this->customer_id])
            ->one()) {
            throw new OrderExistsException($unparsedOrder['id']);
        }

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
