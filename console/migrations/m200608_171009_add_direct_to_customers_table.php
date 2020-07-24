<?php

use yii\db\Migration;

/**
 * Class m200608_171009_add_direct_to_customers_table
 */
class m200608_171009_add_direct_to_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customers}}', 'direct',$this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
