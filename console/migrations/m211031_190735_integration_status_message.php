<?php

use yii\db\Migration;

/**
 * Class m211031_190735_integration_status_message
 */
class m211031_190735_integration_status_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%integration}}", "status_message", "string");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%integration}}", "status_message");
    }

}
