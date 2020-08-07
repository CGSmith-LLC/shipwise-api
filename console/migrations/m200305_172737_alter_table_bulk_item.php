<?php

use yii\db\Migration;

/**
 * Class m200305_172737_alter_table_bulk_item
 *
 * This migration is to add new column to `bulk_item` table.
 */
class m200305_172737_alter_table_bulk_item extends Migration
{

    public $tableName = '{{%bulk_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'job', $this
            ->string(255)
            ->defaultValue(null)
            ->after('order_id')
            ->comment('Job name'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'job');
    }

}
