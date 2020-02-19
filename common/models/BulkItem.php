<?php

namespace common\models;

use common\models\base\BaseBulkItem;

/**
 * Class BulkItem
 *
 * @package common\models
 *
 * @property string $statusName
 * @property string $statusColor
 */
class BulkItem extends BaseBulkItem
{

    const STATUS_QUEUED = 0;
    const STATUS_DONE   = 1;
    const STATUS_ERROR  = 2;

    /**
     * @var array
     */
    private static $statusNames = [
        self::STATUS_QUEUED => 'Queued',
        self::STATUS_DONE   => 'Done',
        self::STATUS_ERROR  => 'Error',
    ];

    /**
     * @var array
     */
    private static $statusColors = [
        self::STATUS_QUEUED => 'warning',
        self::STATUS_DONE   => 'success',
        self::STATUS_ERROR  => 'danger',
    ];

    /**
     * @return string
     */
    public function getStatusName()
    {
        return self::$statusNames[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusColor()
    {
        return self::$statusColors[$this->status];
    }
}
