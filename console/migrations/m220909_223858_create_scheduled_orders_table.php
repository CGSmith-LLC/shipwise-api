<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scheduled_orders}}`.
 */
class m220909_223858_create_scheduled_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%scheduled_orders}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'scheduled_date' =>  $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%scheduled_orders}}');
    }
}
