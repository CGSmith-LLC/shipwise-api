<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%behavior}}`.
 */
class m211115_005035_create_behavior_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%behavior}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'integration_id' => $this->integer(11)->notNull(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(255),
            'event' => $this->string(128)->notNull(),
            'status' => $this->integer(11)->notNull(),
            'order' => $this->integer(11)->notNull(),
            'created_at' => $this->datetime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->datetime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%behavior}}');
    }
}
