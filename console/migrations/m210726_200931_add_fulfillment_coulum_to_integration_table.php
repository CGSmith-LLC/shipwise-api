<?php

use yii\db\Migration;

/**
 * Class m210726_200931_add_fulfillment_coulum_to_integration_table
 */
class m210726_200931_add_fulfillment_coulum_to_integration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%integration}}", "fulfillment", "string");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%integration}}", "fulfillment");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210726_200931_add_fulfillment_coulum_to_integration_table cannot be reverted.\n";

        return false;
    }
    */
}
