<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m200504_210905_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->string(120)->notNull()->comment('Reference to customer'),
            'next_invoice' => $this->date()->notNull()->comment('The Next Date to generate an invoice'),
            'months_to_recur' => $this->integer(2)->notNull()->comment('How many months will be used to calculate the next invoice'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscription}}');
    }
}
