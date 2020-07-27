<?php

use yii\db\Migration;

/**
 * Class m180715_010226_alter_table_api_consumer
 */
class m180715_010226_alter_table_api_consumer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn('{{%api_consumer}}', 'token_generated_on', 'last_activity');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->renameColumn('{{%api_consumer}}', 'last_activity', 'token_generated_on');
    }
}
