<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%webhook_log}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%webhook}}`
 */
class m221208_225254_create_webhook_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%webhook_log}}', [
            'id' => $this->primaryKey(),
            'webhook_id' => $this->integer()->notNull(),
            'response' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `webhook_id`
        $this->createIndex(
            '{{%idx-webhook_log-webhook_id}}',
            '{{%webhook_log}}',
            'webhook_id'
        );

        // add foreign key for table `{{%webhook}}`
        $this->addForeignKey(
            '{{%fk-webhook_log-webhook_id}}',
            '{{%webhook_log}}',
            'webhook_id',
            '{{%webhook}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%webhook}}`
        $this->dropForeignKey(
            '{{%fk-webhook_log-webhook_id}}',
            '{{%webhook_log}}'
        );

        // drops index for column `webhook_id`
        $this->dropIndex(
            '{{%idx-webhook_log-webhook_id}}',
            '{{%webhook_log}}'
        );

        $this->dropTable('{{%webhook_log}}');
    }
}
