<?php


namespace common\adapters;


use common\models\Order;

class ColdcoAdapter implements FulfillmentAdapter
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

        // Cartonization

        // Sending the Order

    }
}