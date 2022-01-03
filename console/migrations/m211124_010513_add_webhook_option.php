<?php

use yii\db\Migration;

/**
 * Class m211124_010513_add_webhook_option
 */
class m211124_010513_add_webhook_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%integration}}', 'webhooks_enabled', $this->boolean()->defaultValue(false)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%integration}}', 'webhooks_enabled');
    }

}
