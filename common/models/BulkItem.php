<?php

namespace common\models;

use common\models\base\BaseBulkItem;

/**
 * Class BulkItem
 *
 * @package common\models
 */
class BulkItem extends BaseBulkItem
{

    const STATUS_QUEUED = 0;
    const STATUS_DONE   = 1;
    const STATUS_ERROR  = 2;
}
