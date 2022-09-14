<?php

namespace common\adapters\ecommerce;

use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderExistsException;
use common\models\Address;
use common\models\forms\OrderForm;
use common\models\Order;
use common\models\Sku;
use common\models\State;
use common\models\Status;
use yii\base\Component;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

class DudaAdapter extends Component
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
        $unparsedOrder = json_decode($unparsedOrder, true);
        $model = new OrderForm();
        $model->order = new Order();
        $model->address = new Address();

        $this->trigger(self::EVENT_BEFORE_PARSE);

        if ($unparsedOrder['paymentStatus'] !== 'PAID') {
            throw new IgnoredWebhookException('Order must be marked paid', 200);
        }

        // check if order exists
        if (Order::find()
            ->where(['customer_reference' => (string) $unparsedOrder['orderNumber']])
            ->andWhere(['customer_id' => $this->customer_id])
            ->one()) {
            throw new OrderExistsException($unparsedOrder['orderNumber']);
        }

        // set order created date
        $createDate = isset($unparsedOrder['createDate']) ? new \DateTime($unparsedOrder['createDate']) : new \DateTime();

        $model->order->setAttributes([
            'customer_id' => $this->customer_id,
            'customer_reference' => (string) $unparsedOrder['orderNumber'],
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

        $unparsedItems = str_replace('"', '', $unparsedOrder['items']);
        $unparsedItems = str_replace('\'', '"', $unparsedItems);
        $patterns = ['/True/', '/False/', '/\\\\/',];
        $replacements = ['true', 'false', '',];
        $unparsedItems = json_decode(preg_replace($patterns, $replacements, $unparsedItems), true);

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

        \Yii::debug($excludedItems);

        foreach ($unparsedItems as $unparsedProduct) {
            if (!in_array($unparsedProduct['sku'], $excludedItems)) {
                if (empty($includedItems) || in_array($unparsedProduct['sku'], $includedItems)) {
                    $items[] = [
                        'quantity' => $unparsedProduct['quantity'],
                        'sku' => $unparsedProduct['sku'],
                        'name' => $unparsedProduct['name'],
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
