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
     *     @SWG\Property( property = "uuid", type = "string", description = "Item UUID from ecommerce system" ),
     *     @SWG\Property( property = "quantity", type = "integer", description = "Quantity" ),
     *     @SWG\Property( property = "sku", type = "string", description = "SKU" ),
     *     @SWG\Property( property = "name", type = "string", description = "Item name" ),
     *     @SWG\Property( property = "alias_quantity", type = "integer", description = "SKU" ),
     *     @SWG\Property( property = "alias_sku", type = "integer", description = "SKU used that is an alias" ),
     *     @SWG\Property( property = "notes", type = "string", description = "Note for item" ),
     *     @SWG\Property( property = "type", type = "string", description = "Type of item (packaging, item, dry ice, etc)" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'uuid', 'quantity', 'sku', 'name', 'alias_sku', 'alias_quantity', 'notes', 'type'];
    }
}