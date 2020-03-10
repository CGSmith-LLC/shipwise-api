<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%items_shipped}}`.
 */
class m200227_153230_create_items_shipped_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%package_items}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'package_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'sku' => $this->string(64)->notNull(),
            'name' => $this->string(128),
            'lot_number' => $this->string(128),
            'serial_number' => $this->string(128),
            'created_date' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%package_items}}');
    }
}
