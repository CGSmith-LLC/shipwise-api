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
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'name'];
    }
}