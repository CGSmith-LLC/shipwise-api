<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%webhook}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%customers}}`
 * - `{{%user}}`
 */
class m221208_225034_create_webhook_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%webhook}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'endpoint' => $this->string()->notNull(),
            'authentication_type' => $this->integer(),
            'signing_secret' => $this->string()->notNull(),
            'user' => $this->string(),
            'pass' => $this->string(),
            'customer_id' => $this->integer()->notNull(),
            'active' => $this->boolean()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `customer_id`
        $this->createIndex(
            '{{%idx-webhook-customer_id}}',
            '{{%webhook}}',
            'customer_id'
        );

        // add foreign key for table `{{%customers}}`
        $this->addForeignKey(
            '{{%fk-webhook-customer_id}}',
            '{{%webhook}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-webhook-user_id}}',
            '{{%webhook}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-webhook-user_id}}',
            '{{%webhook}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%customers}}`
        $this->dropForeignKey(
            '{{%fk-webhook-customer_id}}',
            '{{%webhook}}'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            '{{%idx-webhook-customer_id}}',
            '{{%webhook}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-webhook-user_id}}',
            '{{%webhook}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-webhook-user_id}}',
            '{{%webhook}}'
        );

        $this->dropTable('{{%webhook}}');
    }
}
