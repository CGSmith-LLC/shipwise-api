<?php

use yii\db\Migration;

/**
 * Class m200218_091918_create_bulk_action_tables
 *
 * Handles creation of tables `bulk_action` and `bulk_item`
 */
class m200218_091918_create_bulk_action_tables extends Migration
{

    public $tableNameMaster = '{{%bulk_action}}';
    public $tableNameDetail = '{{%bulk_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // master table
        $this->createTable($this->tableNameMaster, [
            'id'         => $this->primaryKey(),
            'code'       => $this->string(60)->notNull()->comment('Code of the bulk action'),
            'name'       => $this->string(120)->notNull()->comment('Name of the bulk action'),
            'status'     => $this->tinyInteger(1)->defaultValue(0)->comment('Current status. 0:processing, 1:completed, 2:error'),
            'created_on' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Created timestamp'),
            'created_by' => $this->integer(11)->defaultValue(null)->comment('ID of the user who created/triggered bulk action'),
        ], " COMMENT 'Bulk actions'");

        // detail table
        $this->createTable($this->tableNameDetail, [
            'id'             => $this->primaryKey(),
            'bulk_action_id' => $this->integer(11)->notNull()->comment('Ref to Bulk Action'),
            'order_id'       => $this->integer(11)->defaultValue(null)->comment('Ref to Order'),
            'queue_id'       => $this->string(60)->defaultValue(null)->comment('Queue message ID if any'),
            'status'         => $this->tinyInteger(1)->defaultValue(0)->comment('Current status. 0:queued, 1:done, 2:error'),
        ], " COMMENT 'Bulk action items (orders)'");

        $this->createIndex('idx-bulk_item-bulk_action_id', $this->tableNameDetail, 'bulk_action_id');
        $this->createIndex('idx-bulk_item-order_id', $this->tableNameDetail, 'order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableNameDetail);
        $this->dropTable($this->tableNameMaster);
    }
}
