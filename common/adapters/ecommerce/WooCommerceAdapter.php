<?php

namespace common\adapters\ecommerce;

use common\behaviors\AddSku;
use common\events\OrderEvent;
use common\models\Address;
use common\models\forms\OrderForm;
use common\models\Order;
use common\models\shipping\Service;
use common\models\Sku;
use common\models\State;
use common\models\Status;
use yii\base\Component;
use yii\console\Exception;

class WooCommerceAdapter extends Component
{
    const EVENT_BEFORE_PARSE = 'beforeParse';
    const EVENT_AFTER_PARSE = 'afterParse';

    /**
     * @param object $unparsedOrder
     * @return Order
     * @throws Exception
     */
    public function parseOrder(object $unparsedOrder): Order
    {
        // find behaviors asociated with this
        $this->attachBehavior('addSkuBehavior', [
            'class' => AddSku::class,
            'unparsedOrder' => $unparsedOrder
        ]);


        $model = new OrderForm();
        $model->order = new Order();
        $model->address = new Address();

        $model->order->status_id = Status::OPEN;
        $model->order->origin = 'WooCommerce';
        $model->order->address_id = 0; // to avoid validation, as we validate address model separately*/
        $this->trigger(self::EVENT_BEFORE_PARSE);
        /**
         *    $shipwiseOrder = new Order();
        $shipwiseOrder->setReferenceNumber($row->number);
        $shipwiseOrder->setUuid((string)$row->id);
        $shipwiseOrder->setStatus(Order::STATUS_OPEN);
        $reflect = new \ReflectionClass($this);
        if ($reflect->getParentClass() !== false) {
        $shipwiseOrder->setOrigin($reflect->getParentClass()->getShortName());
        } else {
        $shipwiseOrder->setOrigin($reflect->getShortName());
        }
        $shipwiseOrder->setNotes(substr($row->customer_note, 0, 139)); // greeting
        $shipwiseOrder->setShipToName($shipFirstname . ' ' . $shipLastname);
        $shipwiseOrder->setShipToAddress1((!empty($row->shipping->address_1) ? $row->shipping->address_1 : $row->billing->address_1));
        $shipwiseOrder->setShipToAddress2((!empty($row->shipping->address_2) ? $row->shipping->address_2 : $row->billing->address_2));
        $shipwiseOrder->setShipToCompany((!empty($row->shipping->company) ? $row->shipping->company : $row->billing->company));
        $shipwiseOrder->setShipToCity((!empty($row->shipping->city) ? $row->shipping->city : $row->billing->city));
        $shipwiseOrder->setShipToState((!empty($row->shipping->state) ? $row->shipping->state : $row->billing->state));
        $shipwiseOrder->setShipToZip((!empty($row->shipping->postcode) ? $row->shipping->postcode : $row->billing->postcode));
        $shipwiseOrder->setShipToPhone((!empty($row->shipping->phone) ? $row->shipping->phone : $row->billing->phone));
        $shipwiseOrder->setShipCarrier(FedEx::ID);
        $shipwiseOrder->setShipService(FedEx::SHIPWISE_FEDEX_GROUND);
        // We initialize the variable
        $order = array();
        foreach ($row->line_items as $item) {
        if (!in_array($item->sku, $this->excludedProducts) && !empty($item->sku)) {
        $orderItem = new OrderedItem();
        $orderItem->setName($item->name);
        $orderItem->setQuantity($item->quantity);
        $orderItem->setSku($item->sku);
        $order['items'][] = $orderItem;
        }
        }
        if (isset($order['items']) && count($order['items'])) {
        $shipwiseOrder->setOrderedItems($order['items']);
        $result = $shipwiseOrder;
        }

         */
       /* $model->setAttributes([

            'shipToZip' => $unparsedOrder->shipping->postcode,
            'shipToPhone' => $unparsedOrder->shipping->phone,
            'shipCarrier',
            'shipService',
        ]);
        \Yii::debug($model);
        die;*/

        $event = new OrderEvent();
        $event->setOrder($unparsedOrder);
        $this->trigger(self::EVENT_AFTER_PARSE, $event);
        echo $unparsedOrder->number;

        return $model;
    }

    protected function buildGeneral(array $json)
    {
        $this->customer_reference = $json['number'];
        $this->UUID = (string)$json['id'];
        $this->origin = "WooCommerce";
        $this->notes = $json['customer_note'];
    }

    protected function buildAddress(array $json)
    {
        $this->shipToEmail = $json['shipping']['email'] ?? $json['billing']['email'];
        $this->shipToPhone = $json['shipping']['phone'] ?? $json['billing']['phone'];
        $this->shipToName = $json['shipping']['first_name'] . ' ' . $json['shipping']['last_name'];
        $this->shipToAddress1 = $json['shipping']['address_1'];
        if (!empty($json['shipping']['address_2'])) {
            $this->shipToAddress2 = $json['shipping']['address_2'];
        }
        $this->shipToCompany = $json['shipping']['company'];
        $this->shipToCity = $json['shipping']['city'];
        $this->shipToState = State::findOne(['abbreviation' => $json['shipping']['state']])->id;
        $this->shipToZip = $json['shipping']['postcode'];
        $this->shipToCountry = $json['shipping']['country'];
    }

    protected function buildShipping(array $json)
    {
        $this->shippingService = Service::findByShipWiseCode('FedExGround')->id;
    }

    protected function buildItems(array $json)
    {
        $this->items = [];
        foreach ($json['line_items'] as $item) {
            if (!in_array($item['sku'], Sku::findAll(condition: ['excluded' => 'true', 'customer_id' => $this->customerID])) && !empty(trim($item['sku']))) {
                $orderItem = [];
                $orderItem["name"] = $item['name'];
                $orderItem["quantity"] = $item['quantity'];
                $orderItem["sku"] = trim($item['sku']);
                $this->items[] = $orderItem;
            }
        }
    }
}