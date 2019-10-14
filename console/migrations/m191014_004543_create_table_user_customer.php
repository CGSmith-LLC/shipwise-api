<?php

use yii\db\Migration;

/**
 * Class m191014_004543_create_table_user_customer
 *
 * This migration is to create table to associate user with one or many customers.
 */
class m191014_004543_create_table_user_customer extends Migration
{
    public $tableName = '{{%user_customer}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->comment('Reference to user'),
            'customer_id' => $this->integer(11)->notNull()->comment('Reference to customer'),
        ], " COMMENT 'Associate user with customers'");

        $this->createIndex("idx-user_customer-user", $this->tableName, 'user_id');
        $this->createIndex("idx-user_customer-customer", $this->tableName, 'customer_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
