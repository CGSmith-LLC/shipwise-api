<?php

use yii\db\Migration;

/**
 * Class m200727_092000_add_index_to_orders_table
 */
class m200727_092000_add_index_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('created_date_idx', '{{%orders}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('created_date_idx', '{{%orders}}', 'created_date');
    }

}
