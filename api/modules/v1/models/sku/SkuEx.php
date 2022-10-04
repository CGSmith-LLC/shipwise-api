<?php
namespace api\modules\v1\models\sku;

use common\models\Sku;

/**
 * Class SkuEx
 *
 * @package api\modules\v1\models\sku
 */
class SkuEx extends Sku
{

    /**
     * @SWG\Definition(
     *     definition = "Sku",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "Identifier of the SKU" ),
     *     @SWG\Property( property = "name", type = "string",  description = "Name of the SKU" ),
     *     @SWG\Property( property = "sku",  type = "string",  description = "Stock keeping unit" ),
     *     @SWG\Property( property = "excluded",  type = "boolean",  description = "SKU that is excluded or included from fulfillment" ),
     *
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id'       => 'id',
            'sku'      => 'sku',
            'name'     => 'name',
            'excluded' => 'excluded',
        ];
    }
}