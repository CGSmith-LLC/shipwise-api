<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customers}}`.
 */
class m200511_194031_add_stripe_customer_id_column_to_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customers}}', 'stripe_customer_id', $this->string(128)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customers}}', 'stripe_customer_id');
    }
}
