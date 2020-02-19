<?php

namespace common\models;

use common\models\base\BaseBulkAction;

/**
 * Class BulkAction
 *
 * @package common\models
 */
class BulkAction extends BaseBulkAction
{

    const STATUS_QUEUED = 0;
    const STATUS_DONE   = 1;
    const STATUS_ERROR  = 2;
}
