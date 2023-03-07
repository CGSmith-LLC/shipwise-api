<?php

namespace common\services\platforms;

use common\models\Address;
use common\models\Item;
use common\models\Order;
use common\models\Sku;
use yii\helpers\ArrayHelper;

/**
 * Class CreateOrderService
 * @package common\services\platforms
 */
class CreateOrderService
{
    protected int $customerId;
    protected Order $order;
    protected Address $address;
    protected array $items;
    protected array $itemsErrors = [];

    public function __construct(int $customerId)
    {
        $this->customerId = $customerId;
        $this->order = new Order();
        $this->address = new Address();
    }

    public function setOrder(array $attributes): void
    {
        $this->order->setAttributes($attributes);
        // Skip validation by `address_id` for the moment (will be set later):
        $this->order->address_id = 0;
    }

    public function setCarrier()
    {

    }

    public function setAddress(array $attributes): void
    {
        $this->address->setAttributes($attributes);
    }

    public function setItems(array $items): void
    {
        $this->items = $items;

        foreach ($this->items as $k => $item) {
            // Skip validation by `order_id` for the moment (will be set later):
            $this->items[$k]['order_id'] = 0;
        }

        $excluded = $this->getExcludedItems();

        foreach ($this->items as $k => $item) {
            if (in_array($item['sku'], $excluded)) {
                unset($this->items[$k]);
            }
        }
    }

    public function getOrderErrors(): array
    {
        return $this->order->getErrors();
    }

    public function getAddressErrors(): array
    {
        return $this->address->getErrors();
    }

    public function getItemsErrors(): array
    {
        return $this->itemsErrors;
    }

    public function isValid(): bool
    {
        $orderIsValid = $this->order->validate();
        $addressIsValid = $this->address->validate();
        $itemsAreValid = true;

        foreach ($this->items as $key => $item) {
            $orderItem = new Item();
            $orderItem->setAttributes($item);

            if (!$orderItem->validate()) {
                $this->itemsErrors[$key] = $orderItem->getErrors();
                $itemsAreValid = false;
            }
        }

        return ($orderIsValid && $addressIsValid && $itemsAreValid);
    }

    public function create(): bool|Order
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->order->save();
        $this->address->save();

        $this->order->address_id = $this->address->id;
        $this->order->save();

        foreach ($this->items as $item) {
            $orderItem = new Item();
            $orderItem->setAttributes($item);
            $orderItem->order_id = $this->order->id;
            $orderItem->save();
        }

        return $this->order;
    }

    protected function getExcludedItems(): array
    {
        return ArrayHelper::map(
            Sku::find()
                ->where(['customer_id' => $this->customerId, 'excluded' => 1])
                ->all(), 'id','sku');
    }

    public static function isOrderExists(array $params): bool
    {
        $exists = Order::find();

        if (isset($params['origin'])) {
            $exists->andWhere(['origin' => $params['origin']]);
        }

        if (isset($params['uuid'])) {
            $exists->andWhere(['uuid' => (string)$params['uuid']]);
        }

        if (isset($params['customer_id'])) {
            $exists->andWhere(['customer_id' => $params['customer_id']]);
        }

        return $exists->exists();
    }
}
