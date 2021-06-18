<?php

use yii\db\Migration;

/**
 * Class m180715_010226_alter_table_api_consumer
 */
class m210614_131847_alter_api_consumer_table_add_label extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%api_consumer}}', 'label', $this->string(128)->defaultValue('Shipwise')->null());
        $this->dropColumn('{{%api_consumer}}', 'auth_token');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%api_consumer}}', 'label');
    }
}
