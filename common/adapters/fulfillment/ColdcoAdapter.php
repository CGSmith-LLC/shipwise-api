<?php


namespace common\adapters;


use common\models\Order;
use yii\base\Component;

class ColdcoAdapter extends FulfillmentAdapter
{
    public const RENO_ID = 2;
    public const ST_LOUIS_ID = 1;

    public function getRequestInfo(Order $order): array
    {
        $config = [];

        switch($order->address->state)
        {
            case 'CA':
            case 'OR':
            case 'WA':
            case 'ID':
            case 'UT':
            case 'AZ':
            case 'AK':
            case 'HI':
            case 'NV':
                $config['facility_id'] = self::RENO_ID;
            break;
            default:
                $config['facility_id'] = self::ST_LOUIS_ID;
                break;
        }

        $config['ship_carrier'] = $order->carrier_id;
        $config['ship_service'] = $order->service_id;

        /* Shipping Stuff, incl.:
         *      Getting & Validating address
         *      Transit time
         */

        // Altering notes

        // Dry Ice logic

		$event = new \CartonizationEvent();
		$event->customer_id = $order->customer_id;
		$event->items = $order->items;
        $this->trigger(self::EVENT_CARTONIZATION, $event);

        // Sending the Order

    }
}