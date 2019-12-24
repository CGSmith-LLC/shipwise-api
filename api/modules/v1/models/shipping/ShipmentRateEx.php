<?php

namespace api\modules\v1\models\shipping;

use common\models\shipping\ShipmentRate;

/**
 * Class ShipmentRateEx
 *
 * @package api\modules\v1\models\shipping
 */
class ShipmentRateEx extends ShipmentRate
{

    /**
     * @SWG\Definition(
     *     definition = "CalculatedRate",
     *
     *     @SWG\Property(
     *          property = "serviceCode",
     *          type = "string",
     *          description = "Service code"
     *      ),
     *     @SWG\Property(
     *          property = "serviceName",
     *          type = "string",
     *          description = "Service name"
     *      ),
     *     @SWG\Property(
     *          property = "totalPrice",
     *          ref = "#/definitions/Money"
     *      ),
     *     @SWG\Property(
     *          property = "detailedCharges",
     *          type = "array",
     *          @SWG\Items( ref = "#/definitions/Charge" )
     *       ),
     *     @SWG\Property(
     *          property = "deliveryTimeStamp",
     *          type = "string",
     *          description = "Delivery timestamp"
     *      ),
     *     @SWG\Property(
     *          property = "deliveryDayOfWeek",
     *          type = "string",
     *          description = "Delivery day of week"
     *      ),
     *     @SWG\Property(
     *          property = "transitTime",
     *          type = "string",
     *          description = "Transit time"
     *      ),
     *     @SWG\Property(
     *          property = "deliveryByTime",
     *          type = "string",
     *          description = "Delivery by time"
     *      ),
     *     @SWG\Property(
     *          property = "infoMessage",
     *          type = "string",
     *          description = "Info message. Relevant notes about this rate."
     *      ),
     * )
     */

    /**
     * @SWG\Definition(
     *     definition = "Money",
     *     @SWG\Property(
     *            property = "amount",
     *            type = "number",
     *            format = "double",
     *            description = "Amount"
     *        ),
     *     @SWG\Property(
     *          property = "currency",
     *          type = "string",
     *          description = "Currency code in ISO 4217",
     *          minLength = 3,
     *          maxLength = 3
     *      ),
     * )
     */

    /**
     * @SWG\Definition(
     *     definition = "Charge",
     *     @SWG\Property(
     *            property = "type",
     *            type = "string",
     *            description = "Charge type"
     *        ),
     *     @SWG\Property(
     *            property = "description",
     *            type = "string",
     *            description = "Charge description"
     *        ),
     *     @SWG\Property(
     *          property = "amount",
     *          ref = "#/definitions/Money"
     *      ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'serviceCode',
            'serviceName',
            'totalPrice',
            'detailedCharges',
            'deliveryTimeStamp',
            'deliveryDayOfWeek',
            'transitTime',
            'deliveryByTime',
            'infoMessage',
        ];
    }
}
