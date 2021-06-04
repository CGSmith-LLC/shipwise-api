<?php

use yii\db\Migration;

/**
 * Class m210331_175922_alter_items_table
 */
class m210331_175922_alter_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%items}}', 'notes', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%items}}', 'notes');
    }
}
