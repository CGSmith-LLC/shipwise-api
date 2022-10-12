<?php
namespace api\modules\v1\models\alias;

use common\models\AliasChildren;
use common\models\AliasParent;

/**
 * Class AliasEx
 *
 * @package api\modules\v1\models\sku
 */
class AliasChildrenEx extends AliasChildren
{

    /**
     * @SWG\Definition(
     *     definition = "AliasChildren",
     *
     *     @SWG\Property( property = "name", type = "string",  description = "Name of the SKU" ),
     *     @SWG\Property( property = "sku",  type = "string",  description = "Stock keeping unit" ),
     *     @SWG\Property( property = "quantity",  type = "string",  description = "Quantity of SKU" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'sku'      => 'sku',
            'name'     => 'name',
            'quantity' => 'quantity',
        ];
    }
}