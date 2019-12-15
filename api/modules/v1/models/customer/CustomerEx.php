<?php

namespace api\modules\v1\models\customer;

use common\models\Customer;

/**
 * Class CustomerEx
 *
 * @package api\modules\v1\models\customer
 */
class CustomerEx extends Customer
{

    /**
     * @SWG\Definition(
     *     definition = "Customer",
     *
     *     @SWG\Property( property = "id",   type = "integer", description = "Identifier of the customer" ),
     *     @SWG\Property( property = "name", type = "string",  description = "Name of the customer" ),
     *     @SWG\Property(
     *            property = "createdDate",
     *            type = "string",
     *            format = "date-time",
     *            description = "Customer creation date-time"
     *        ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id'          => 'id',
            'name'        => 'name',
            'createdDate' => 'created_date',
        ];
    }
}
