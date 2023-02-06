<?php

namespace common\models;

use common\models\base\BaseCustomer;
use common\traits\CacheableListTrait;

/**
 * Class Customer
 *
 * @property string $country Country two-chars ISO code
 *
 * @package common\models
 */
class Customer extends BaseCustomer
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'customers-list';

    public string $country = 'US';
}
