<?php

use yii\db\Migration;

/**
 * Class m220909_214520_add_clone_order_preference_profile
 */
class m220909_214520_add_clone_order_preference_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'clone_order_preference', $this->integer()->defaultValue(9)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'clone_order_preference');
    }

}
