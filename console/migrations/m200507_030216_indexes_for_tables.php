<?php

use yii\db\Migration;

/**
 * Class m200507_030216_indexes_for_tables
 */
class m200507_030216_indexes_for_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // SELECT * FROM `orders` WHERE (`orders`.`customer_reference`='5171890') AND (`orders`.`customer_id`=1)
        $this->createIndex('customer_reference_idx', '{{%orders}}', ['customer_reference', 'customer_id']);

        // SELECT * FROM `package_items_lot_info` WHERE `package_items_id`=71349
        $this->createIndex('package_items_id_idx', '{{%package_items_lot_info}}', 'package_items_id');

        // SELECT * FROM `packages` WHERE `order_id`=790305
        $this->createIndex('order_id_idx', '{{%packages}}', 'order_id');

        // SELECT * FROM `items` WHERE `order_id`=784453
        $this->createIndex('order_id_idx', '{{%items}}', 'order_id');

        // SELECT * FROM `order_history` WHERE `order_id`=784037
        $this->createIndex('order_id_idx', '{{%order_history}}', 'order_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200507_030216_indexes_for_tables cannot be reverted.\n";

        return false;
    }

}
