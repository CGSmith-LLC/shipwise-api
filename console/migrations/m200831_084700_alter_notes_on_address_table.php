<?php

use yii\db\Migration;

/**
 * Class m200831_084700_alter_notes_on_address_table
 */
class m200831_084700_alter_notes_on_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%addresses}}', 'notes', $this->string(600)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%addresses}}', 'notes', $this->string(140)->null());
    }

}
