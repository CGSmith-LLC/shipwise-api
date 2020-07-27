<?php

use yii\db\Migration;

/**
 * Class m200608_171009_add_direct_to_customers_table
 */
class m200720_073100_add_country_to_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%addresses}}', 'country', $this->string(2)->notNull()->defaultValue('US'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%addresses}}', 'country');
    }
}