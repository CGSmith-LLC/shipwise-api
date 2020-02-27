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
     *     definition = "Package",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Package ID" ),
     *     @SWG\Property( property = "tracking", type = "string", description = "Tracking Number" ),
     *     @SWG\Property( property = "length", type = "string", description = "Length" ),
     *     @SWG\Property( property = "width", type = "string", description = "Width" ),
     *     @SWG\Property( property = "height", type = "string", description = "Height" ),
     *     @SWG\Property( property = "weight", type = "string", description = "Weight" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'tracking', 'length', 'width', 'height', 'weight'];
    }

}