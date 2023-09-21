<?php

use yii\db\Migration;

/**
 * Class m230921_062521_add_execution_link_and_last_error
 */
class m230921_062521_add_execution_link_and_last_error extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'execution_log', $this->string(500)->null());
        $this->addColumn('{{%orders}}', 'error_log', $this->string(500)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'execution_log');
        $this->dropColumn('{{%orders}}', 'error_log');
    }

}
