<?php

use yii\db\Migration;

/**
 * Class m211102_083339_fulltext_search_syntax_update
 */
class m211102_083339_fulltext_search_syntax_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('SET @ft_boolean_syntax = \'+ ><()~*:""&|\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('SET @ft_boolean_syntax = \'+ -><()~*:""&|\';');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211102_083339_fulltext_search_syntax_update cannot be reverted.\n";

        return false;
    }
    */
}
