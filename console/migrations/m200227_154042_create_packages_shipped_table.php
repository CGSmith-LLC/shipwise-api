<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%packages_shipped}}`.
 */
class m200227_154042_create_packages_shipped_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%packages}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'tracking' => $this->string(128),
            'length' => $this->string(16),
            'width' => $this->string(16),
            'height' => $this->string(16),
            'weight' => $this->string(16),
            'created_date' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%packages}}');
    }
}
