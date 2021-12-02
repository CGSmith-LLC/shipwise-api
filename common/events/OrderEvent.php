<?php


namespace common\events;


use common\models\Order;

class OrderEvent extends \yii\base\Event
{
    private $_order;

    /**
     * @return
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param $order
     */
    public function setOrder( $order): void
    {
        $this->_order = $order;
    }
}