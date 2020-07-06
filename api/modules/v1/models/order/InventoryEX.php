<?php

namespace api\modules\v1\models\order;

use common\models\base\BaseInventory;

/**
 * Class ItemEx
 *
 * @package api\modules\v1\models\order
 */
class InventoryEX extends BaseInventory
{

    /**
     * @SWG\Definition(
     *     definition = "Inventory",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "ID" ),
     *
     *     @SWG\Property( property = "available_quantity", type = "number", description = "Available Quantity" ),
     *     @SWG\Property( property = "sku", type = "string", description = "SKU" ),
     *     @SWG\Property( property = "name", type = "string", description = " name" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'customer_id', 'available_quantity', 'sku', 'name'];
    }
}