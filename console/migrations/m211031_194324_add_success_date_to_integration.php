<?php

use yii\db\Migration;

/**
 * Class m211031_194324_add_success_date_to_integration
 */
class m211031_194324_add_success_date_to_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%integration}}', 'last_success_run', $this->dateTime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%integration}}', 'last_success_run');
    }

}
