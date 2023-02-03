<?php

namespace common\models;

use common\models\base\BaseStatus;
use common\traits\CacheableListTrait;
use yii\helpers\ArrayHelper;

/**
 * Class Status
 *
 * @package common\models
 */
class Status extends BaseStatus
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'statuses-list';

    /* Please keep synchronized with db values! */
    const SHIPPED = 1;
    const ON_HOLD = 6;
    const CANCELLED = 7;
    const PENDING = 8;
    const OPEN = 9;
    const WMS_ERROR = 10;
    const COMPLETED = 11;

    public function init(): void
    {
        $this->setClearCacheEvents();
        parent::init();
    }

    /**
     * Get array of Status ids
     *
     * @return array
     */
    public static function getIdsAsArray()
    {
        return ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
    }

    /**
     * Status label
     *
     * @param bool $html Whether to return in html format
     *
     * @return string
     */
    public function getStatusLabel($html = true)
    {
        $label = match ($this->id) {
            self::OPEN => 'primary',
            self::PENDING => 'info',
            self::SHIPPED => 'success',
            self::WMS_ERROR => 'danger',
            default => 'default',
        };

        return $html ? '<p class="label label-' . $label . '">' . $this->name . '</p>' : $this->name;

    }
}