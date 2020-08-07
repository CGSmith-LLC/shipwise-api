<?php

use yii\db\Migration;

/**
 * Class m200806_213621_create_table_batch_item
 */
class m200806_213621_create_table_batch_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%batch_item}}', [
            'id' => $this->primaryKey(),
            'batch_id' => $this->integer(11)->notNull(),
            'order_id' => $this->integer(11)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%batch_item}}');
    }
}
