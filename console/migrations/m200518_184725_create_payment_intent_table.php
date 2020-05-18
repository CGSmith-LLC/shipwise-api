<?php

use yii\db\Migration;

/**
 * Class m200518_184725_create_payment_intent_table
 */
class m200518_184725_create_payment_intent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('payment_intent', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull()->comment('Reference to customer'),
            'payment_method_id' => $this->integer(11)->notNull()->comment('Reference to payment method table'),
            'invoice_id' => $this->integer(11)->notNull(),
            'stripe_payment_intent_id' => $this->char(128)->null()->comment('stripe payment intent id'),
            'amount' => $this->integer(11)->notNull()->comment('Total in Cents'),
            'status' => $this->string(64)->notNull()->comment('Stripe Status of Payment Intent'),
            'created_date' => $this->date()->notNull()->comment('Created Date'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment_intent}}');
    }
}
