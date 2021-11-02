<?php

use yii\db\Migration;

/**
 * Class m211029_124728_orders_fulltext_search
 */
class m211029_124728_orders_fulltext_search extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE orders ADD FULLTEXT(customer_reference)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211029_124728_orders_fulltext_search cannot be reverted.\n";

        return false;
    }
}
