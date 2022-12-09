<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%webhook_trigger}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%webhook}}`
 * - `{{%status}}`
 */
class m221208_230340_create_webhook_trigger_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%webhook_trigger}}', [
            'id' => $this->primaryKey(),
            'webhook_id' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `webhook_id`
        $this->createIndex(
            '{{%idx-webhook_trigger-webhook_id}}',
            '{{%webhook_trigger}}',
            'webhook_id'
        );

        // add foreign key for table `{{%webhook}}`
        $this->addForeignKey(
            '{{%fk-webhook_trigger-webhook_id}}',
            '{{%webhook_trigger}}',
            'webhook_id',
            '{{%webhook}}',
            'id',
            'CASCADE'
        );

        // creates index for column `status_id`
        $this->createIndex(
            '{{%idx-webhook_trigger-status_id}}',
            '{{%webhook_trigger}}',
            'status_id'
        );

        // add foreign key for table `{{%status}}`
        $this->addForeignKey(
            '{{%fk-webhook_trigger-status_id}}',
            '{{%webhook_trigger}}',
            'status_id',
            '{{%status}}',
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
            '{{%fk-webhook_trigger-webhook_id}}',
            '{{%webhook_trigger}}'
        );

        // drops index for column `webhook_id`
        $this->dropIndex(
            '{{%idx-webhook_trigger-webhook_id}}',
            '{{%webhook_trigger}}'
        );

        // drops foreign key for table `{{%status}}`
        $this->dropForeignKey(
            '{{%fk-webhook_trigger-status_id}}',
            '{{%webhook_trigger}}'
        );

        // drops index for column `status_id`
        $this->dropIndex(
            '{{%idx-webhook_trigger-status_id}}',
            '{{%webhook_trigger}}'
        );

        $this->dropTable('{{%webhook_trigger}}');
    }
}
