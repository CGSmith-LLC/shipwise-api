<?php

use yii\db\Migration;

/**
 * Class m180715_010226_alter_table_api_consumer
 */
class m210617_075327_alter_api_consumer_tbale_encrypted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%api_consumer}}', 'auth_secret', 'encrypted_secret' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%api_consumer}}', 'encrypted_secret', 'auth_secret');
    }
}
