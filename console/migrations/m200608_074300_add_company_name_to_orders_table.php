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
        $this->addColumn('{{%addresses}}', 'company', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%addresses}}', 'company');
    }

}
