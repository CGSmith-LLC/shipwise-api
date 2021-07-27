<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fulfillment}}`.
 */
class m210727_140743_create_fulfillment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fulfillment}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fulfillment}}');
    }
}
