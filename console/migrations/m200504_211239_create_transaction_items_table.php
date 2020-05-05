<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction_items}}`.
 */
class m200504_211239_create_transaction_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction_items}}', [
            'id' => $this->primaryKey(),
            'subscription_items_id' => $this->integer(11)->notNull()->comment('Reference to subscription_items_id'),
            'name' => $this->string(120)->notNull()->comment('Name'),
            'amount' => $this->integer(11)->notNull()->comment('Comment'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transaction_items}}');
    }
}
