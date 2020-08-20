<?php

namespace api\modules\v1\models\order;

use common\models\PackageItemLotInfo;

/**
 * Class PackageItemLotInfoEx
 *
 * @package api\modules\v1\models\order
 */
class PackageItemLotInfoEx extends PackageItemLotInfo
{

    /**
     * @SWG\Definition(
     *     definition = "PackageItemLotInfoEx",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Package Item Lot Info ID" ),
     *     @SWG\Property( property = "lot_number", type = "string", description = "Lot Number" ),
     *     @SWG\Property( property = "serial_number", type = "string", description = "Serial Number" ),
     *     @SWG\Property( property = "quantity", type = "integer", description = "Quantity" )
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'lot_number', 'serial_number', 'quantity'];
    }
}