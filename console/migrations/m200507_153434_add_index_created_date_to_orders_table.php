<?php

use yii\db\Migration;

/**
 * Class m200507_153434_add_index_created_date_to_orders_table
 */
class m200507_153434_add_index_created_date_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('created_date_idx', '{{%orders}}', 'created_date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200507_153434_add_index_created_date_to_orders_table cannot be reverted.\n";

        return false;
    }

}
