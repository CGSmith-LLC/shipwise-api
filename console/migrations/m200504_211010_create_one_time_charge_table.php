<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%one_time_charge}}`.
 */
class m200504_211010_create_one_time_charge_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%one_time_charge}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull()->comment('Reference to customer'),
            'one_time_item_name' => $this->string(64)->notNull()->comment(''),
            'amount' => $this->integer(11)->notNull(),
            'added_to_transaction' => $this->boolean()->notNull()->defaultValue(0),
        ]);
    }


}
