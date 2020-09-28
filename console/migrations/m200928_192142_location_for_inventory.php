<?php

use yii\db\Migration;

/**
 * Class m200928_192142_location_for_inventory
 */
class m200928_192142_location_for_inventory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%inventory}}', 'location', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%inventory}}', 'location');
    }
}
