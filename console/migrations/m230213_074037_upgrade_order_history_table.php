<?php

use yii\db\Migration;

/**
 * Class m230213_074037_upgrade_order_history_table
 */
class m230213_074037_upgrade_order_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->truncateTable("{{%order_history}}");

        $this->dropIndex("index2", "{{%order_history}}");
        $this->dropIndex("order_id_idx", "{{%order_history}}");
        $this->dropColumn("{{%order_history}}", "status_id");

        $this->renameColumn("{{%order_history}}", "comment", "notes");

        $this->execute("ALTER TABLE {{%order_history}} ADD `user_id` INT NOT NULL AFTER `id`;");
        $this->execute("ALTER TABLE {{%order_history}} ADD `username` VARCHAR(255) NOT NULL AFTER `user_id`;");

        $this->addForeignKey(
            '{{%fk-order_history-order_id}}',
            '{{%order_history}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-order_history-user_id}}',
            '{{%order_history}}',
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
        return;
    }
}
