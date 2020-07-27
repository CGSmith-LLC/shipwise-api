<?php

use yii\db\Migration;

/**
 * Class m200303_000401_update_items_table
 */
class m200303_000401_update_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%items}}', 'uuid', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%items}}', 'uuid');
    }
}
