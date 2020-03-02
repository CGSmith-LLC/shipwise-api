<?php

use yii\db\Migration;

/**
 * Class m200301_053834_alter_bulk_item_table
 *
 * This migration is to add new columns to `bulk_item` table.
 */
class m200301_053834_alter_bulk_item_table extends Migration
{

    public $tableName = '{{%bulk_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn($this->tableName, 'errors', $this
            ->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext') // MEDIUMTEXT – 16MB (16,777,215 characters)
            ->defaultValue(null)
            ->after('base64_filetype')
            ->comment('Processing error messages encoded in JSON'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'error');
    }
}
