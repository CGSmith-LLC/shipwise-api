<?php

use common\models\Customer;
use Stripe\Exception\ApiErrorException;
use yii\base\Event;
use yii\db\Migration;

/**
 * Class m200626_134938_create_stripe_id_for_existing_users
 */
class m200626_134938_create_stripe_id_for_existing_users extends Migration
{
    /**
     * {@inheritdoc}
     *
     * @param $event Event
     * @throws ApiErrorException
     */


    public function safeUp()
    {
        $customers = Customer::find()
            ->where(['stripe_customer_id' => null])
            ->all();


        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $customer->stripeCreate();
            var_dump($customer->stripe_customer_id);
            $customer->save();

            var_dump($customer->errors);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
