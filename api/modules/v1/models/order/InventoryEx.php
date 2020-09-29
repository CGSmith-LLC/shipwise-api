<?php

namespace api\modules\v1\models\order;

use common\models\Inventory;

/**
 * Class ItemEx
 *
 * @package api\modules\v1\models\order
 */
class InventoryEx extends Inventory
{

    /**
     * @SWG\Definition(
     *     definition = "Inventory",
     *
     *     @SWG\Property( property = "available_quantity", type = "number", description = "Available Quantity" ),
     *     @SWG\Property( property = "sku", type = "string", description = "SKU" ),
     *     @SWG\Property( property = "name", type = "string", description = "name" ),
     *     @SWG\Property( property = "location", type = "string", description = "Warehouse identifier" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'sku', 'name', 'location', 'available_quantity'];
    }
}