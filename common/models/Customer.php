<?php

namespace common\models;

use common\models\base\BaseCustomer;

/**
 * Class Customer
 *
 * @property string $country Country two-chars ISO code
 *
 * @package common\models
 */
class Customer extends BaseCustomer
{

    public $country = 'US';

}
