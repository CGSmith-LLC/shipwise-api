<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue}}`.
 */
class m200215_012018_create_queue_table extends Migration
{

    public $tableName = '{{%queue}}';
    public $tableOptions;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id'          => $this->primaryKey(),
            'channel'     => $this->string()->notNull(),
            'job'         => $this->binary()->notNull(),
            'pushed_at'   => $this->integer()->notNull(),
            'ttr'         => $this->integer()->notNull(),
            'delay'       => $this->integer()->notNull(),
            'priority'    => $this->integer()->unsigned()->notNull()->defaultValue(1024),
            'reserved_at' => $this->integer(),
            'attempt'     => $this->integer(),
            'done_at'     => $this->integer(),
        ]);
        $this->createIndex('channel', $this->tableName, 'channel');
        $this->createIndex('reserved_at', $this->tableName, 'reserved_at');
        $this->createIndex('priority', $this->tableName, 'priority');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
