<?php

use yii\db\Migration;

/**
 * Class m200803_125456_sku_table
 */
class m200803_125456_sku_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sku}}', [
            'id' =>$this->primaryKey(),
            'sku' => $this -> string(16)->null(),
            'name' => $this->string(64)->null(),

            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sku}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200803_125456_sku_table cannot be reverted.\n";

        return false;
    }
    */
}
