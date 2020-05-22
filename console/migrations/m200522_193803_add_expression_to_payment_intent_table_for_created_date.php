<?php

use yii\db\Migration;

/**
 * Class m200522_193803_add_expression_to_payment_intent_table_for_created_date
 */
class m200522_193803_add_expression_to_payment_intent_table_for_created_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%payment_intent}}', 'created_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%payment_intent}}', 'created_date', $this->date()->notNull()->comment('Created Date'));
    }
}
