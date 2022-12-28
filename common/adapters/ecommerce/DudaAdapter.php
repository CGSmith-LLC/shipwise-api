<?php

namespace common\adapters\ecommerce;

use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderCancelledException;
use common\exceptions\OrderExistsException;
use common\models\Address;
use common\models\forms\OrderForm;
use common\models\Order;
use common\models\Sku;
use common\models\State;
use common\models\Status;
use common\models\UnparsedProductEvent;
use console\jobs\orders\CancelOrderJob;
use yii\base\Component;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

class DudaAdapter extends Component
{
    const EVENT_BEFORE_PARSE = 'beforeParse';
    const EVENT_BEFORE_ITEM_PARSE = 'beforeItemParse';
    const EVENT_AFTER_PARSE = 'afterParse';

    public int $customer_id;

    /**
     * @param object $unparsedOrder
     * @throws Exception
     */
    public function parseOrder($unparsedOrder)
    {
        $unparsedOrder = json_decode($unparsedOrder, true);
        $model = new OrderForm();
        $model->order = new Order();
        $model->address = new Address();

        $this->trigger(self::EVENT_BEFORE_PARSE);

        if ($unparsedOrder['paymentStatus'] === 'CANCELLED') {
            \Yii::$app->queue->push(new CancelOrderJob([
                'customer_reference' => $unparsedOrder['id'],
                'customer_id' => $this->customer_id,
            ]));
            throw new OrderCancelledException();
        }
        if ($unparsedOrder['paymentStatus'] !== 'PAID') {
            throw new IgnoredWebhookException('Order must be marked paid', 200);
        }

        // check if order exists
        if (Order::find()
            ->where(['customer_reference' => (string) $unparsedOrder['id']])
            ->andWhere(['customer_id' => $this->customer_id])
            ->one()) {
            throw new OrderExistsException($unparsedOrder['orderNumber']);
        }

        // set order created date
        $createDate = isset($unparsedOrder['createDate']) ? new \DateTime($unparsedOrder['createDate']) : new \DateTime();

        $model->order->setAttributes([
            'customer_id' => $this->customer_id,
            'customer_reference' => (string) $unparsedOrder['id'],
            'status_id' => Status::OPEN,
            'uuid' => (string) $unparsedOrder['id'],
            'created_date' => $createDate->format('Y-m-d'),
            'origin' => 'Duda',
            'address_id' => 0, // avoid validation issues
        ]);

        /** @var State $state */
        $state = State::findByAbbrOrName($unparsedOrder['shippingPerson']['countryCode'], abbr: $unparsedOrder['shippingPerson']['stateOrProvinceCode']);
        $model->address->setAttributes([
            'name' => $unparsedOrder['shippingPerson']['firstName'] . ' ' . $unparsedOrder['shippingPerson']['lastName'],
            //'company' => $unparsedOrder['shippingPerson']['company'],
            'address1' => $unparsedOrder['shippingPerson']['street'],
            'city' => $unparsedOrder['shippingPerson']['city'],
            'state_id' => $state->id,
            'country' => $state->country,
            'zip' => $unparsedOrder['shippingPerson']['postalCode'],
            'phone' => (!empty($unparsedOrder['shippingPerson']['phone'])) ? $unparsedOrder['shippingPerson']['phone'] : '555-555-5555',
        ]);

        $excludedItems = ArrayHelper::map(
            Sku::find()
                ->where(['customer_id' => $this->customer_id])
                ->andWhere(['excluded' => 1])
                ->all(), 'id','sku');
        $includedItems = ArrayHelper::map(
            Sku::find()
                ->where(['customer_id' => $this->customer_id])
                ->andWhere(['excluded' => 0])
                ->all(), 'id','sku');

        foreach ($unparsedOrder['items'] as $unparsedProduct) {
            if (!in_array($unparsedProduct['sku'], $excludedItems)) {
                if (empty($includedItems) || in_array($unparsedProduct['sku'], $includedItems)) {
                    $event = new UnparsedProductEvent();
                    $event->unparsedItem = $unparsedProduct;
                    \Yii::debug($event, 'beforeParse');
                    $this->trigger(self::EVENT_BEFORE_ITEM_PARSE, $event);
                    \Yii::debug($event, 'afterParse');
                    $items[] = [
                        'quantity' => $event->unparsedItem['quantity'],
                        'sku' => $event->unparsedItem['sku'],
                        'name' => $event->unparsedItem['name'],
                    ];
                }
            }
        }
        $model->setItems($items);
        $model->validate();

        // @todo convert from a bigcommerce mapping
        //$order->setShipCarrier(FedEx::ID);
        //$order->setShipService(FedEx::SHIPWISE_GROUND_HOME);

        return $model;
    }

}
