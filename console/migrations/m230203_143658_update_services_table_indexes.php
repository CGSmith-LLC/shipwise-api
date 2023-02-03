<?php

use yii\db\Migration;

/**
 * Class m230203_143658_update_services_table_indexes
 */
class m230203_143658_update_services_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%service}} ADD INDEX(`name`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%service}} DROP INDEX `name`;");
    }
}
