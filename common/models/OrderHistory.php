<?php

namespace common\models;

use Yii;
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

    public Order $order;

    /**
     * Add values like self::SCENARIO_ORDER_VIEWED to the array
     * if you want to disable specific scenarios
     */
    public static array $disabledScenarios = [self::SCENARIO_ORDER_VIEWED];

    /**
     * Add attributes you want to skip when adding to the logs
     */
    protected array $skipAttributes = [
        'created_date', 'updated_date'
    ];

    public function scenarios(): array
    {
        return [
            self::SCENARIO_ORDER_CREATED => [],
            self::SCENARIO_ORDER_VIEWED => [],
            self::SCENARIO_ORDER_CHANGED => [],
            self::SCENARIO_ORDER_STATUS_CHANGED => [],
        ];
    }

    public function init(): void
    {
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'orderHistoryPopulate']);
        parent::init();
    }

    public static function isScenarioEnabled(string $scenario): bool
    {
        return !in_array($scenario, self::$disabledScenarios);
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
        $this->order_id = $this->order->id;

        if (!$this->user_id) {
            if (!Yii::$app->user->isGuest) {
                $this->user_id = Yii::$app->user->id;
            }
        }

        if (!$this->username) {
            if (!Yii::$app->user->isGuest) {
                $this->username = Yii::$app->user->identity->username;
            }
        }

        match ($this->scenario) {
            self::SCENARIO_ORDER_CREATED => $this->orderCreated(),
            self::SCENARIO_ORDER_VIEWED => $this->orderViewed(),
            self::SCENARIO_ORDER_CHANGED => $this->orderChanged(),
            self::SCENARIO_ORDER_STATUS_CHANGED => $this->orderStatusChanged(),
            default => throw new \InvalidArgumentException(),
        };
    }

    protected function orderCreated(): void
    {
        $orderAttributes = $this->getSanitisedAttributes($this->order->attributes);
        $addressAttributes = $this->getSanitisedAttributes($this->order->address->attributes);
        $orderItems = $this->order->items;

        foreach ($orderItems as $key => $orderItem) {
            $orderItems[$key] = $this->getSanitisedAttributes($orderItem->attributes);
        }

        $this->notes = "Order #{$this->order->id} is created.";
        $this->notes .= "\r\n\r\nOrder Attributes: " . Json::encode($orderAttributes, JSON_PRETTY_PRINT);
        $this->notes .= "\r\n\r\nAddress Attributes: " . Json::encode($addressAttributes, JSON_PRETTY_PRINT);
        $this->notes .= "\r\n\r\nItems: " . Json::encode($orderItems, JSON_PRETTY_PRINT);
    }

    protected function orderViewed(): void
    {
        $this->notes = "Order #{$this->order->id} is viewed.";
    }

    protected function orderChanged(): void
    {
        $this->notes = "Order #{$this->order->id} is changed.";
    }

    protected function orderStatusChanged(): void
    {
        $this->notes = "Order status #{$this->order->id} is changed. Previous status: . New status: .";
    }

    protected function getSanitisedAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->skipAttributes)) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }
}
