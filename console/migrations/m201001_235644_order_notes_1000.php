<?php

use yii\db\Migration;

/**
 * Class m201001_235644_order_notes_1000
 */
class m201001_235644_order_notes_1000 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(1000)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(600)->null());
    }

}
