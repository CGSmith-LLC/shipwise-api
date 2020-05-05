<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invoice}}`.
 */
class m200504_210925_create_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invoice}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull()->comment('Reference to customer'),
            'subscription_id' => $this->integer(11)->notNull()->comment('Reference to Subscription ID'),
            'customer_name' => $this->string(64)->notNull()->comment('Customer Name'),
            'amount' => $this->integer(11)->notNull()->comment('Total in Cents'),
            'balance' => $this->integer(11)->notNull()->comment('Balance Due in Cents'),
            'due_date' => $this->date()->notNull()->comment('Due Date'),
            'stripe_charge_id' => $this->char(128)->null()->comment('stripe charge id'),
            'status' => $this->integer(2)->notNull()->comment('Status of transaction'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%invoice}}');
    }
}
