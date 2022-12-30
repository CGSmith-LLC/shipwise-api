<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%warehouse}}`.
 */
class m221230_194520_create_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%warehouse}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%user_warehouse}}', [
            'id' => $this->primaryKey(),
            'warehouse_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);

        $this->addColumn('{{%orders}}', 'warehouse_id', $this->integer()->null());

        // creates index for column `warehouse_id`
        $this->createIndex(
            '{{%idx-orders-warehouse_id}}',
            '{{%orders}}',
            'warehouse_id'
        );

        // add foreign key for table `{{%orders}}`
        $this->addForeignKey(
            '{{%fk-orders-warehouse_id}}',
            '{{%orders}}',
            'warehouse_id',
            '{{%orders}}',
            'id',
            'RESTRICT'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%orders}}`
        $this->dropForeignKey(
            '{{%fk-orders-warehouse_id}}',
            '{{%orders}}'
        );

        // drops index for column `warehouse_id`
        $this->dropIndex(
            '{{%idx-orders-warehouse_id}}',
            '{{%orders}}'
        );

        $this->dropColumn('{{%orders}}', 'warehouse_id');
        $this->dropTable('{{%warehouse}}');
        $this->dropTable('{{%user_warehouse}}');
    }
}
