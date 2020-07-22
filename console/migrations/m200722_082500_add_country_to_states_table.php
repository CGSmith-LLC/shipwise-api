<?php

use yii\db\Migration;

/**
 * Class m200608_171009_add_direct_to_customers_table
 */
class m200722_082500_add_country_to_states_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%states}}', 'country', $this->string(2)->notNull()->defaultValue('US'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%states}}', 'country');
    }
}