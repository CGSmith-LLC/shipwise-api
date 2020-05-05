<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction}}`.
 */
class m200504_210925_create_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull()->comment('Name of Transaction'),
            'customer_id' => $this->integer(11)->notNull()->comment('Reference to customer'),
            'subscription_id' => $this->integer(11)->notNull()->comment('Reference to Subscription ID'),
            'customer_name' => $this->string(64)->notNull()->comment('Customer Name'),
            'due_date' => $this->integer(11)->notNull()->comment('Due Date'),
            'stripe_charge_id' => $this->integer(11)->notNull()->comment(),
            'status' => $this->string(64)->notNull()->comment(''),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transaction}}');
    }
}
