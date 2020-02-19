<?php

namespace common\models;

use common\models\base\BaseBulkAction;
use Yii;

/**
 * Class BulkAction
 *
 * @package common\models
 *
 * @property string $statusName
 * @property string $statusColor
 */
class BulkAction extends BaseBulkAction
{

    const STATUS_PROCESSING = 0;
    const STATUS_COMPLETED  = 1;
    const STATUS_ERROR      = 2;

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->created_by = Yii::$app->user->id ?? null;
        }

        return true;
    }

    /**
     * @var array
     */
    private static $statusNames = [
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED  => 'Completed',
        self::STATUS_ERROR      => 'Error',
    ];

    /**
     * @var array
     */
    private static $statusColors = [
        self::STATUS_PROCESSING => 'warning',
        self::STATUS_COMPLETED  => 'success',
        self::STATUS_ERROR      => 'danger',
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

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status == self::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * @return bool|\DateInterval
     * @throws \Exception
     */
    public function processingSince()
    {
        $startedAt = new \DateTime($this->created_on);
        return $startedAt->diff(new \DateTime("now"));
    }
}
