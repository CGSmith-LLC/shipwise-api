<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shopify_app}}`.
 */
class m200812_204202_create_shopify_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shopify_app}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'shop' => $this->string(128)->notNull(),
            'scopes' => $this->string(128)->notNull(),
            'access_token' => $this->string(128)->null(),
            'created_date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shopify_app}}');
    }
}
