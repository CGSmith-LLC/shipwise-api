<?php

use yii\db\Migration;

/**
 * Class m210720_193447_create_customer_meta
 */
class m210720_193447_create_integration_meta extends Migration
{
    public $tableName = '{{%integration_meta}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id'             => $this->primaryKey(),
            'integration_id' => $this->integer()->notNull(),
            'key'            => $this->string()->notNull(),
            'value'          => $this->string()->notNull(),
            'created_date'   => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210720_193447_create_customer_meta cannot be reverted.\n";

        return false;
    }
    */
}
