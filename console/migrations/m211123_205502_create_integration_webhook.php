<?php

use yii\db\Migration;

/**
 * Class m211123_205502_create_integration_webhook
 */
class m211123_205502_create_integration_webhook extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%integration_webhook}}', [
            'id' => $this->primaryKey(),
            'integration_id' => $this->integer(11)->notNull(),
            'integration_hookdeck_id' => $this->integer(11)->notNull(),
            'source_uuid' => $this->string(255)->notNull(),
            'name' => $this->string(128)->notNull(),
            'topic' => $this->string(255)->notNull(),
            'created_at' => $this->datetime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->datetime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%integration_webhook}}');
    }
}
