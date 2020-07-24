<?php

use yii\db\Migration;

/**
 * Class m190423_163854_origin_on_orders
 * Add origin field on orders table
 */
class m190423_163854_origin_on_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'origin', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'origin');
    }
}
