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

    final const STATUS_PROCESSING = 0;
    final const STATUS_COMPLETED  = 1;
    final const STATUS_ERROR      = 2;

    final const PRINT_MODE_QZ  = 1; // Print using qz plugin (thermal printer)
    final const PRINT_MODE_PDF = 2; // Print as one combined PDF file

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
     * @return bool
     */
    public function isProcessed()
    {
        return $this->status != self::STATUS_PROCESSING;
    }

    /**
     * @return \DateInterval
     * @throws \Exception
     */
    public function processingSince(): \DateInterval
    {
        $startedAt = new \DateTime($this->created_on);
        return $startedAt->diff(new \DateTime("now"));
    }

    /**
     * Mark as completed
     * (no saving performed)
     *
     * @return $this
     */
    public function markCompleted()
    {
        $this->status = self::STATUS_COMPLETED;

        return $this;
    }

    /**
     * Mark as failed
     * (no saving performed)
     *
     * @return $this
     */
    public function markFailed()
    {
        $this->status = self::STATUS_ERROR;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $result = $this->deleteRelatedEntities();

            return $result;
        } else {
            return false;
        }
    }

    /**
     * Delete all related entities.
     *
     * @return boolean
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function deleteRelatedEntities()
    {
        $result = true;

        // Items
        foreach ($this->items as $item) {
            $result = $result && $item->delete();
        }

        return $result;
    }
}
