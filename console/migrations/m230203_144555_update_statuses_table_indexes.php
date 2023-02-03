<?php

use yii\db\Migration;

/**
 * Class m230203_144555_update_statuses_table_indexes
 */
class m230203_144555_update_statuses_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%status}} ADD INDEX(`name`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%status}} DROP INDEX `name`;");
    }
}
