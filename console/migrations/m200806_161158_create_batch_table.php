<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%batch}}`.
 */
class m200806_161158_create_batch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%batch}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'customer_id' => $this->integer(11)->notNull(),
            'created_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%batch}}');
    }
}
