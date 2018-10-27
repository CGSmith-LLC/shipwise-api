<?php

use yii\db\Migration;

/**
 * Class m181027_174724_add_requested_ship_date
 */
class m181027_174724_add_requested_ship_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'requested_ship_date',
            $this->dateTime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'requested_ship_date');
    }

}
