<?php
namespace api\modules\v1\models\alias;

use common\models\AliasChildren;
use common\models\AliasParent;

/**
 * Class AliasEx
 *
 * @package api\modules\v1\models\sku
 */
class AliasEx extends AliasParent
{

    /**
     * @SWG\Definition(
     *     definition = "Alias",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "Identifier of the SKU" ),
     *     @SWG\Property( property = "name", type = "string",  description = "Name of the SKU" ),
     *     @SWG\Property( property = "sku",  type = "string",  description = "Stock keeping unit" ),
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
            'children' => 'children',
        ];
    }

    public function getChildren()
    {
        return $this->hasMany(AliasChildrenEx::class, ['alias_id' => 'id']);
    }
}