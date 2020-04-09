<?php

use yii\db\Migration;

/**
 * Class m200220_201116_alter_customer_table
 *
 * This migration is to add new columns to `customers` table.
 */
class m200220_201116_alter_customer_table extends Migration
{

    public $tableName = '{{%customers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Add new columns
         */
        $this->addColumn($this->tableName, 'address1', $this
            ->string(64)
            ->notNull()
            ->after('name')
            ->comment('Address line 1'));

        $this->addColumn($this->tableName, 'address2', $this
            ->string(64)
            ->defaultValue(null)
            ->after('address1')
            ->comment('Address line 2'));

        $this->addColumn($this->tableName, 'city', $this
            ->string(64)
            ->notNull()
            ->after('address2')
            ->comment('City'));

        $this->addColumn($this->tableName, 'state_id', $this
            ->integer(11)
            ->notNull()
            ->after('city')
            ->comment('State ID'));
        $this->createIndex('idx-customers-state_id', $this->tableName, 'state_id');

        $this->addColumn($this->tableName, 'zip', $this
            ->string(16)
            ->notNull()
            ->after('state_id')
            ->comment('ZIP code'));

        $this->addColumn($this->tableName, 'logo', $this
            ->string(256)
            ->notNull()
            ->after('zip')
            ->comment('The absolute URL of the logo'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'address1');
        $this->dropColumn($this->tableName, 'address2');
        $this->dropColumn($this->tableName, 'city');
        $this->dropIndex('idx-customers-state_id', $this->tableName);
        $this->dropColumn($this->tableName, 'state_id');
        $this->dropColumn($this->tableName, 'zip');
        $this->dropColumn($this->tableName, 'logo');
    }
}
