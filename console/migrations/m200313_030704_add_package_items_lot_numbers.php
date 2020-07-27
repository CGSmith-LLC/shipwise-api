<?php

use yii\db\Migration;

/**
 * Class m200313_030704_add_package_items_lot_numbers
 */
class m200313_030704_add_package_items_lot_numbers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%package_items_lot_info}}', [
            'id'               => $this->primaryKey(),
            'package_items_id' => $this->integer()->notNull(),
            'quantity'         => $this->integer()->notNull(),
            'lot_number'       => $this->string(128),
            'serial_number'    => $this->string(128),
            'created_date'     => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200313_030704_add_package_items_lot_numbers cannot be reverted.\n";

        return false;
    }

}
