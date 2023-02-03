<?php

use yii\db\Migration;

/**
 * Class m230203_133507_update_orders_table_indexes
 */
class m230203_133507_update_orders_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%orders}} ADD INDEX(`po_number`);");
        $this->execute("ALTER TABLE {{%orders}} ADD INDEX(`carrier_id`);");
        $this->execute("ALTER TABLE {{%orders}} ADD INDEX(`service_id`);");
        $this->execute("ALTER TABLE {{%orders}} ADD INDEX(`address_id`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%orders}} DROP INDEX `po_number`;");
        $this->execute("ALTER TABLE {{%orders}} DROP INDEX `carrier_id`;");
        $this->execute("ALTER TABLE {{%orders}} DROP INDEX `service_id`;");
        $this->execute("ALTER TABLE {{%orders}} DROP INDEX `address_id`;");
    }
}
