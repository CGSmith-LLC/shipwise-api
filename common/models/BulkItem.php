<?php

namespace common\models;

use common\models\base\BaseBulkItem;
use Yii;

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

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // insert scenario
        } else {
            // update scenario

            /**
             * If all Bulk Items finished processing, then update Bulk Action status.
             * BulkItem status values are: 0:queued, 1:done, 2:error
             */
            $itemsStatusSum = self::find()->where(['bulk_action_id' => $this->bulk_action_id])->sum('status');
            $nbItems        = count($this->bulkAction->items);
            if ($itemsStatusSum == $nbItems) {
                $this->bulkAction->markCompleted()->save();
            } elseif ($itemsStatusSum > $nbItems) {
                $this->bulkAction->markFailed()->save();
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
