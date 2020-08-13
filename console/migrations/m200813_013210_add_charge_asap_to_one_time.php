<?php

use yii\db\Migration;

/**
 * Class m200813_013210_add_charge_asap_to_one_time
 */
class m200813_013210_add_charge_asap_to_one_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%one_time_charge}}', 'charge_asap', $this->tinyInteger(1)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%one_time_charge}}', 'charge_asap');

    }

}
