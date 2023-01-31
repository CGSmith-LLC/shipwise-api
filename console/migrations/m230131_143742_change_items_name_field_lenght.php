<?php

use yii\db\Migration;

/**
 * Class m230131_143742_change_items_name_field_lenght
 */
class m230131_143742_change_items_name_field_lenght extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(128));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230131_143742_change_items_name_field_lenght cannot be reverted.\n";

        return false;
    }
    */
}
