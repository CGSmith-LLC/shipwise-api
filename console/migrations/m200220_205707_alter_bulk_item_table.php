<?php

use yii\db\Migration;

/**
 * Class m200220_205707_alter_bulk_item_table
 *
 * This migration is to add new columns to `bulk_item` table.
 */
class m200220_205707_alter_bulk_item_table extends Migration
{

    public $tableName = '{{%bulk_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Add new columns
         */
        $this->addColumn($this->tableName, 'base64_filedata', $this
            ->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext') // MEDIUMTEXT – 16MB (16,777,215 characters)
            ->defaultValue(null)
            ->after('queue_id')
            ->comment('File encoded in base64'));

        $this->addColumn($this->tableName, 'base64_filetype', $this
            ->string(6)
            ->defaultValue(null)
            ->after('base64_filedata')
            ->comment('Type of encoded file: PDF, PNG.'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'base64_filedata');
        $this->dropColumn($this->tableName, 'base64_filetype');
    }

}
