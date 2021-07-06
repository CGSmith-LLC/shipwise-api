<?php

use yii\db\Migration;

/**
 * Class m210706_194745_order_date_index
 */
class m210706_194745_order_date_index extends Migration
/** adds index to created date column on orders table */
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('date_idx', '{{%orders}}', 'created_date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('date_idx', '{{%orders}}');
    }
}
