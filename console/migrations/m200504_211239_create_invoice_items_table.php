<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction_items}}`.
 */
class m200504_211239_create_invoice_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invoice_items}}', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer(11)->notNull()->comment('Reference to invoice table'),
            'name' => $this->string(128)->notNull(),
            'amount' => $this->integer(11)->notNull()->comment('cents'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%invoice_items}}');
    }
}
