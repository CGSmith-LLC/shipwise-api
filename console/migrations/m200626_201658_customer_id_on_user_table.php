<?php

use yii\db\Migration;

/**
 * Class m200626_201658_customer_id_on_user_table
 */
class m200626_201658_customer_id_on_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'customer_id', $this->integer(11)
            ->notNull()
            ->defaultValue(0)
            ->comment('User is associated with this customer as it\'s parent'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'customer_id');
    }

}
