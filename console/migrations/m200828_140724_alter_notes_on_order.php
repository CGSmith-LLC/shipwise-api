<?php

use yii\db\Migration;

/**
 * Class m200828_140724_alter_notes_on_order
 */
class m200828_140724_alter_notes_on_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(600)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(140)->null());
    }

}
