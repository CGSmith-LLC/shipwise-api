<?php

namespace api\modules\v1\models\order;

use common\models\Package;

/**
 * Class PackageEx
 *
 * @package api\modules\v1\models\order
 */
class PackageEx extends Package
{

    /**
     * @SWG\Definition(
     *     definition = "Package Shipped",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Package ID" ),
     *     @SWG\Property( property = "tracking", type = "string", description = "Tracking Number" ),
     *     @SWG\Property( property = "length", type = "string", description = "Length" ),
     *     @SWG\Property( property = "width", type = "string", description = "Width" ),
     *     @SWG\Property( property = "height", type = "string", description = "Height" ),
     *     @SWG\Property( property = "weight", type = "string", description = "Weight" ),
     *     @SWG\Property( property = "created_date", type = "string", description = "Created Date" ),
     *     @SWG\Property(
     *          property = "items",
     *          type = "array",
     *          @SWG\Items( ref = "#/definitions/PackageItemEx" )
     *     ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'order_id', 'tracking', 'length', 'width', 'height', 'weight', 'created_date', 'items'];
    }

    /**
     * Get items from the package
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('api\modules\v1\models\order\PackageItemEx', ['package_id' => 'id']);
    }

}