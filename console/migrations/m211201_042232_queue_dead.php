<?php

use yii\db\Migration;

/**
 * Class m211201_042232_queue_dead
 */
class m211201_042232_queue_dead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue_dead}}', [
            'id' => $this->primaryKey(),
            'error' => $this->string(),
            'job' => $this->binary()->notNull(),
            'created_date' => $this->datetime()->notNull()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_dead}}');
    }
}
