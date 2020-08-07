<?php

namespace common\models;

use common\models\base\BaseCustomer;
use Stripe\Stripe;
use yii\helpers\ArrayHelper;

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

    public function init()
    {
        parent::init();

        Stripe::setApiKey(\Yii::$app->stripe->privateKey);

    }

    /**
     * Call stripe to create the customer and set our attribute to the stripe token
     *
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function stripeCreate()
    {
        $customer = \Stripe\Customer::create([
            'name' => $this->name,
        ]);

        /** @var $customer Customer */
        $this->setAttribute('stripe_customer_id', $customer->id);
    }
        /**
         * Returns list of Countries as array [abbreviation=>name]
         *
         * @param string $keyField   Field name to use as key
         * @param string $valueField Field name to use as value
         *
         * @return array
         */
        public static function getList($keyField = 'id', $valueField = 'name')
    {
        $data = self::find()->orderBy([$valueField => SORT_ASC])->all();

        return ArrayHelper::map($data, $keyField, $valueField);
    }

}
