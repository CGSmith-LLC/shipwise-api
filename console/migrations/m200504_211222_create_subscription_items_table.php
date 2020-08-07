<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription_items}}`.
 */
class m200504_211222_create_subscription_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription_items}}', [
            'id' => $this->primaryKey(),
            'subscription_id' => $this->integer(11)->notNull()->comment('Reference to subscriptions'),
            'name' => $this->string(128)->notNull(),
            'amount' => $this->integer(11)->notNull()->comment('amount in cents'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscription_items}}');
    }
}
