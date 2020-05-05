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
            'name' => $this->string(128)->notNull(),
            'amount' => $this->integer(11)->notNull()->comment('In cents'),
            'added_to_invoice' => $this->boolean()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%one_time_charge}}');
    }
}
