<?php

use yii\db\Migration;

/**
 * Class m230203_143131_update_carriers_table_indexes
 */
class m230203_143131_update_carriers_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%carrier}} ADD INDEX(`name`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%carrier}} DROP INDEX `name`;");
    }
}
