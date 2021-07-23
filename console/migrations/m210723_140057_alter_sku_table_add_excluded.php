<?php

use yii\db\Migration;

/**
 * Class m210723_140057_alter_sku_table_add_excluded
 */
class m210723_140057_alter_sku_table_add_excluded extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sku}}', 'excluded', 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210723_140057_alter_sku_table_add_excluded cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210723_140057_alter_sku_table_add_excluded cannot be reverted.\n";

        return false;
    }
    */
}
