<?php

use yii\db\Migration;

/**
 * Class m200812_140710_shopify_webhook_table
 */
class m200812_140710_shopify_webhook_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shopify_webhook}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'shopify_webhook_id' => $this->string(64)->notNull(),
            'created_date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shopify_webhook}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200812_140710_shopify_webhook_table cannot be reverted.\n";

        return false;
    }
    */
}
