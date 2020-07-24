<?php

use yii\db\Migration;

/**
 * This migration adds new column to `bulk_action` table.
 */
class m200407_214047_alter_bulk_action_table extends Migration
{

    public $tableName = '{{%bulk_action}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'print_mode', $this
            ->tinyInteger()
            ->defaultValue(null)
            ->after('status')
            ->comment('Printing mode. 1: qz plugin, 2: pdf file'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'print_mode');
    }

}
