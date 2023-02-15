<?php

namespace common\models;

use common\models\base\BaseItem;
use common\traits\AttachableOrderItemEventsTrait;

/**
 * Class Item
 *
 * @package common\models
 */
class Item extends BaseItem
{
    use AttachableOrderItemEventsTrait;

    public function init(): void
    {
        $this->attachEvents();

        parent::init();
    }
}
