<?php

namespace common\adapters\ecommerce;

use common\events\OrderEvent;
use common\models\Address;
use common\models\forms\OrderForm;
use common\models\Order;
use common\models\Status;
use yii\base\Component;
use yii\console\Exception;

class BigCommerceAdapter extends Component
{
    const EVENT_BEFORE_PARSE = 'beforeParse';
    const EVENT_AFTER_PARSE = 'afterParse';

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

        $model->setOrder([
            'customer_id' => 7, // TODO
            'customer_reference' =>  $unparsedOrder['id'],
            'status_id' => Status::OPEN,
            'uuid' => $unparsedOrder['id'],
            'created_date' => $createDate,
            'origin' => 'BigCommerce',
            'address_id' => 0, // avoid validation issues
        ]);


        $model->setAddress([
            'name' => $unparsedOrder['shipping_addresses'][0]['first_name'] .
                      ' ' . $unparsedOrder['shipping_addresses'][0]['last_name'],
            'company' => $unparsedOrder['shipping_addresses'][0]['company'],
            'address1' => $unparsedOrder['shipping_addresses'][0]['street_1'],
            'address2' => $unparsedOrder['shipping_addresses'][0]['street_2'],
            'city' => $unparsedOrder['shipping_addresses'][0]['city'],
            'state' => $unparsedOrder['shipping_addresses'][0]['state'],
            'country' => $unparsedOrder['shipping_addresses'][0]['country_iso2'],
            'zip' => $unparsedOrder['shipping_addresses'][0]['zip'],
            'phone' => $unparsedOrder['shipping_addresses'][0]['phone'],
        ]);

        foreach ($unparsedOrder['products'] as $unparsedProduct) {
            $items[] = [
                'quantity' => $unparsedProduct['quantity'],
                'sku' => $unparsedProduct['sku'],
                'name' => $unparsedProduct['name'],
            ];
        }
        $model->setItems($items);

        // @todo convert from a bigcommerce mapping
        //$order->setShipCarrier(FedEx::ID);
        //$order->setShipService(FedEx::SHIPWISE_GROUND_HOME);

        $event = new OrderEvent();
        $event->setOrder($unparsedOrder);
        $this->trigger(self::EVENT_AFTER_PARSE, $event);

        return $model;
    }

}