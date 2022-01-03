<?php

use yii\db\Migration;

/**
 * Class m210817_140856_expand_fulfillment_meta_value_length
 */
class m210817_140856_expand_fulfillment_meta_value_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn(table: 'fulfillment_meta', column: 'value', type: 'varchar(2047) not null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn(table: 'fulfillment_meta', column: 'value', type: 'varchar(255) not null');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210817_140856_expand_fulfillment_meta_value_length cannot be reverted.\n";

        return false;
    }
    */
}
