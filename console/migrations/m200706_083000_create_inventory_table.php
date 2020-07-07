<?php

use yii\db\Migration;

/**
 * Class m200706_083000_create_inventory_table
 */
class m200706_083000_create_inventory_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('inventory', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'name' => $this->string(64)->null(),
            'sku' => $this->string(64)->notNull(),
            'available_quantity' => $this->decimal(8, 2)->defaultValue(0)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inventory}}');
    }
}