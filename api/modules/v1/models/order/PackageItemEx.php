<?php

namespace api\modules\v1\models\order;

use common\models\PackageItem;

/**
 * Class PackageItemEx
 *
 * @package api\modules\v1\models\order
 */
class PackageItemEx extends PackageItem
{

    /**
     * @SWG\Definition(
     *     definition = "PackageItemEx",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Package Item ID" ),
     *     @SWG\Property( property = "name", type = "string", description = "Name" ),
     *     @SWG\Property( property = "sku", type = "string", description = "SKU" ),
     *     @SWG\Property( property = "quantity", type = "integer", description = "Quantity" ),
     *     @SWG\Property(
     *          property = "lot_info",
     *          type = "array",
     *          @SWG\Items( ref = "#/definitions/PackageItemLotInfoEx" )
     *     ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'name', 'quantity', 'sku', 'lot_info'];
    }

    /**
     * Get lot info from the package item
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLot_info()
    {
        return $this->hasMany('api\modules\v1\models\order\PackageItemLotInfoEx', ['package_items_id' => 'id']);
    }
}