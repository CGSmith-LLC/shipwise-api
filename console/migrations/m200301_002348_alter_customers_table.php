<?php

use yii\db\Migration;

/**
 * Class m200301_002348_alter_customers_table
 *
 * This migration is to add new columns to `customers` and `addresses` tables.
 */
class m200301_002348_alter_customers_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customers}}', 'phone', $this
            ->string(32)
            ->defaultValue(null)
            ->after('zip')
            ->comment('Phone number'));

        $this->addColumn('{{%customers}}', 'email', $this
            ->string(255)
            ->defaultValue(null)
            ->after('phone')
            ->comment('Email address'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customers}}', 'zip');
        $this->dropColumn('{{%customers}}', 'phone');
    }

}
