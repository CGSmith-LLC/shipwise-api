<?php

namespace api\modules\v1\models\order;

use common\models\Status;

/**
 * Class StatusEx
 *
 * @package api\modules\v1\models\order
 */
class StatusEx extends Status
{
    /**
     * @SWG\Definition(
     *     definition = "OrderStatus",
     *
     *     @SWG\Property( property = "value", type = "integer", description = "Order Status ID" ),
     *     @SWG\Property( property = "display_label", type = "string", description = "Order Status name" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        return ['id', 'name'];
    }

    public static function getListForCsvBox(): array
    {
        $statuses = self::getList();
        $data = [];

        foreach ($statuses as $id => $name) {
            $data[] = ['value' => (string)$id, 'display_label' => $name];
        }

        return $data;
    }
}