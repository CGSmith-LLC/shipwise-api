<?php

use yii\db\Migration;

/**
 * Class m211123_205636_create_integration_hookdeck
 */
class m211123_205636_create_integration_hookdeck extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%integration_hookdeck}}', [
            'id' => $this->primaryKey(),
            'integration_id' => $this->integer(11)->notNull(),
            'source_name' => $this->string(128)->notNull(),
            'source_id' => $this->string(255)->notNull(),
            'source_url' => $this->string(255)->notNull(),
            'destination_name' => $this->string(128)->notNull(),
            'destination_id' => $this->string(255)->notNull(),
            'destination_url' => $this->string(255)->notNull(),
            'created_at' => $this->datetime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->datetime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%integration_hookdeck}}');
    }
}
