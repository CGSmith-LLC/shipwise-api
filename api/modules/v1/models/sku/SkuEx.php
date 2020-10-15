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
     *     @SWG\Property( property = "substitute_1",  type = "string",  description = "Unit to substitute if out of stock" ),
     *     @SWG\Property( property = "substitute_2",  type = "string",  description = "Unit to substitute if out of stock" ),
     *     @SWG\Property( property = "substitute_3",  type = "string",  description = "Unit to substitute if out of stock" ),
     *
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id'           => 'id',
            'sku'          => 'sku',
            'name'         => 'name',
            'substitute_1' => 'substitute_1',
            'substitute_2' => 'substitute_2',
            'substitute_3' => 'substitute_3',
        ];
    }
}