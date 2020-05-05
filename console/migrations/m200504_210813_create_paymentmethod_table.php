<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%paymentmethod}}`.
 */
class m200504_210813_create_paymentmethod_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%paymentmethod}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->null()->comment('Reference to customer'),
            'stripe_payment_method_id' => $this->char(128)->null(),
            'default' => $this->boolean()->notNull()->comment('Is this the customer\'s default payment method?'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%paymentmethod}}');
    }
}
