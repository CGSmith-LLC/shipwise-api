<?php

use yii\db\Migration;

/**
 * Class m230109_011242_add_type_to_user_table
 */
class m230109_011242_add_type_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'type', $this->integer(11)
            ->notNull()
            ->defaultValue(0)
            ->comment('User type association - 0 = customer, 1 = warehouse'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'type');
    }
}
