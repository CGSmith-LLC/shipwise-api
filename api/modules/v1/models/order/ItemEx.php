<?php

namespace api\modules\v1\models\order;

use common\models\Item;

/**
 * Class ItemEx
 *
 * @package api\modules\v1\models\order
 */
class ItemEx extends Item
{

    /**
     * @SWG\Definition(
     *     definition = "Item",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Item ID" ),
     *     @SWG\Property( property = "quantity", type = "integer", description = "Quantity" ),
     *     @SWG\Property( property = "sku", type = "string", description = "SKU" ),
     *     @SWG\Property( property = "name", type = "string", description = "Item name" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'quantity', 'sku', 'name'];
    }
}