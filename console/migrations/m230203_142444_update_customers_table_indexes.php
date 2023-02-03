<?php

use yii\db\Migration;

/**
 * Class m230203_142444_update_customers_table_indexes
 */
class m230203_142444_update_customers_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%customers}} ADD INDEX(`name`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%customers}} DROP INDEX `name`;");
    }
}
