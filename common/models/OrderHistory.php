<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use common\models\base\BaseOrderHistory;
use yii\helpers\Json;

/**
 * Class OrderHistory
 * @package common\models
 */
class OrderHistory extends BaseOrderHistory
{
    public const SCENARIO_ORDER_CREATED = 'scenarioOrderCreated';
    public const SCENARIO_ORDER_VIEWED = 'scenarioOrderViewed';
    public const SCENARIO_ORDER_CHANGED = 'scenarioOrderChanged';
    public const SCENARIO_ORDER_STATUS_CHANGED = 'scenarioOrderStatusChanged';
    public const SCENARIO_ORDER_ADDRESS_CREATED = 'scenarioOrderAddressCreated';
    public const SCENARIO_ORDER_ADDRESS_CHANGED = 'scenarioOrderAddressChanged';
    public const SCENARIO_ORDER_ITEM_ADDED = 'scenarioOrderItemAdded';
    public const SCENARIO_ORDER_ITEM_CHANGED = 'scenarioOrderItemChanged';
    public const SCENARIO_ORDER_ITEM_DELETED = 'scenarioOrderItemDeleted';

    public ?Order $order = null;
    public ?Item $item = null;
    public ?Address $address = null;
    public ?int $previousStatusId = null;
    public ?array $changedAttributes = null;

    protected array $skipAttributes = [
        'created_date',
        'updated_date',
    ];

    public function scenarios(): array
    {
        return [
            self::SCENARIO_ORDER_CREATED => [],
            self::SCENARIO_ORDER_VIEWED => [],
            self::SCENARIO_ORDER_CHANGED => [],
            self::SCENARIO_ORDER_STATUS_CHANGED => [],
            self::SCENARIO_ORDER_ADDRESS_CREATED => [],
            self::SCENARIO_ORDER_ADDRESS_CHANGED => [],
            self::SCENARIO_ORDER_ITEM_ADDED => [],
            self::SCENARIO_ORDER_ITEM_CHANGED => [],
            self::SCENARIO_ORDER_ITEM_DELETED => [],
        ];
    }

    public function init(): void
    {
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'orderHistoryPopulate']);
        parent::init();
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne('common\models\Order', ['id' => 'order_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne('frontend\models\User', ['id' => 'user_id']);
    }

    protected function orderHistoryPopulate(): void
    {
        $this->order_id = null;

        if ($this->order) {
            $this->order_id = $this->order->id;
        } elseif ($this->item) {
            $this->order_id = $this->item->order_id;
        }

        if (!$this->user_id) {
            if (!Yii::$app->user->isGuest) {
                $this->user_id = Yii::$app->user->id;
            }
        }

        match ($this->scenario) {
            self::SCENARIO_ORDER_CREATED => $this->orderCreated(),
            self::SCENARIO_ORDER_VIEWED => $this->orderViewed(),
            self::SCENARIO_ORDER_CHANGED => $this->orderChanged(),
            self::SCENARIO_ORDER_STATUS_CHANGED => $this->orderStatusChanged(),
            self::SCENARIO_ORDER_ADDRESS_CREATED => $this->orderAddressCreated(),
            self::SCENARIO_ORDER_ADDRESS_CHANGED => $this->orderAddressChanged(),
            self::SCENARIO_ORDER_ITEM_ADDED => $this->orderItemAdded(),
            self::SCENARIO_ORDER_ITEM_CHANGED => $this->orderItemChanged(),
            self::SCENARIO_ORDER_ITEM_DELETED => $this->orderItemDeleted(),
            default => throw new \InvalidArgumentException(),
        };
    }

    protected function orderCreated(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        $orderAttributes = $this->sanitizeAttributes($this->order->attributes);
        $this->notes = "Order #{$this->order->id} is created.";
        $this->notes .= "\r\nOrder Attributes: " . Json::encode($orderAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderViewed(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        $this->notes = "Order #{$this->order->id} is viewed.";
    }

    protected function orderChanged(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        if (!$this->changedAttributes) {
            throw new InvalidConfigException("Variable \$changedAttributes id missed.");
        }

        $changedAttributes = $this->sanitizeAttributes($this->changedAttributes);

        $this->notes = "Order #{$this->order->id} is changed.";
        $this->notes .= "\r\nChanged Attributes: " . Json::encode($changedAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderStatusChanged(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        if (!$this->previousStatusId) {
            throw new InvalidConfigException("Variable \$previousStatusId id missed.");
        }

        $prevStatusId = $this->previousStatusId;
        $newStatusId = $this->order->status_id;
        $statuses = Status::getList();

        $this->notes = "Order #{$this->order->id} status is changed.";
        $this->notes .= " Previous status: #{$prevStatusId} ({$statuses[$prevStatusId]}).";
        $this->notes .= " New status: #{$newStatusId}. ({$statuses[$newStatusId]}).";
    }

    protected function orderAddressCreated(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        $addressAttributes = $this->sanitizeAttributes($this->order->address->attributes);
        $this->notes = "Order #{$this->order->id} address (Ship To) is added.";
        $this->notes .= "\r\nAddress Attributes: " . Json::encode($addressAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderAddressChanged(): void
    {
        if (!$this->order) {
            throw new InvalidConfigException("Object Order id missed.");
        }

        if (!$this->address) {
            throw new InvalidConfigException("Object Address id missed.");
        }

        if (!$this->changedAttributes) {
            throw new InvalidConfigException("Variable \$changedAttributes id missed.");
        }

        $changedAttributes = $this->sanitizeAttributes($this->changedAttributes);

        $this->notes = "Address #{$this->address->id} is changed.";
        $this->notes .= "\r\nChanged Attributes: " . Json::encode($changedAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderItemAdded(): void
    {
        if (!$this->item) {
            throw new InvalidConfigException("Object Item id missed.");
        }

        $itemAttributes = $this->sanitizeAttributes($this->item->attributes);
        $this->notes = "Item #{$this->item->id} is added.";
        $this->notes .= "\r\nItem Attributes: " . Json::encode($itemAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderItemChanged(): void
    {
        if (!$this->item) {
            throw new InvalidConfigException("Object Item id missed.");
        }

        if (!$this->changedAttributes) {
            throw new InvalidConfigException("Variable \$changedAttributes id missed.");
        }

        $changedAttributes = $this->sanitizeAttributes($this->changedAttributes);

        $this->notes = "Item #{$this->item->id} is changed.";
        $this->notes .= "\r\nChanged Attributes: " . Json::encode($changedAttributes, JSON_PRETTY_PRINT);
    }

    protected function orderItemDeleted(): void
    {
        if (!$this->item) {
            throw new InvalidConfigException("Object Item id missed.");
        }

        $itemAttributes = $this->sanitizeAttributes($this->item->attributes);
        $this->notes = "Item #{$this->item->id} is deleted.";
        $this->notes .= "\r\nItem Attributes: " . Json::encode($itemAttributes, JSON_PRETTY_PRINT);
    }

    protected function sanitizeAttributes(array $attributes): array
    {
        if ($this->skipAttributes) {
            foreach ($attributes as $key => $value) {
                if (in_array($key, $this->skipAttributes)) {
                    unset($attributes[$key]);
                }
            }
        }

        return $attributes;
    }
}
