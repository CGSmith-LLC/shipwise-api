<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%paymentmethod}}`.
 */
class m200504_210813_create_paymentmethod_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%paymentmethod}}', [
            'id' => $this->primaryKey(),
            'customer_id'=>$this->integer(11)->defaultValue(null)->comment('Reference to customer'),
            'stripe_charge_id'=>$this->integer(11)->notNull()->comment(''),
            'default'=>$this->string(120)->notNull()->comment('Name of the bulk action'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%paymentmethod}}');
    }
}
