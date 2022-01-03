<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fulfillment_meta}}`.
 */
class m210727_140813_create_fulfillment_meta_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fulfillment_meta}}', [
            'id'             => $this->primaryKey(),
            'fulfillment_id' => $this->integer()->notNull(),
            'key'            => $this->string()->notNull(),
            'value'          => $this->string()->notNull(),
            'created_date'   => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fulfillment_meta}}');
    }
}