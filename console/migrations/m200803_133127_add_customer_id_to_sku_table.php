<?php

use yii\db\Migration;

/**
 * Class m200803_133127_add_customer_id_to_sku_table
 */
class m200803_133127_add_customer_id_to_sku_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sku}}', 'customer_id', $this->integer(11)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sku}}','customer_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200803_133127_add_customer_id_to_sku_table cannot be reverted.\n";

        return false;
    }
    */
}
