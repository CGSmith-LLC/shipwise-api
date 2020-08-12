<?php

use yii\db\Migration;

/**
 * Class m200812_014115_add_from_address_to_orders
 */
class m200812_014115_add_from_address_to_orders extends Migration
{
    public $tableName = '{{%orders}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'ship_from_name', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_address1', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_address2', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_city', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_state_id', $this->integer(11)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_zip', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_country_code', $this->string(3)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_phone', $this->string(64)->defaultValue(null)->null());
        $this->addColumn($this->tableName, 'ship_from_email', $this->string(64)->defaultValue(null)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'ship_from_name');
        $this->dropColumn($this->tableName, 'ship_from_address1');
        $this->dropColumn($this->tableName, 'ship_from_address2');
        $this->dropColumn($this->tableName, 'ship_from_city');
        $this->dropColumn($this->tableName, 'ship_from_state_id');
        $this->dropColumn($this->tableName, 'ship_from_zip');
        $this->dropColumn($this->tableName, 'ship_from_country_code');
        $this->dropColumn($this->tableName, 'ship_from_phone');
        $this->dropColumn($this->tableName, 'ship_from_email');
    }

}
