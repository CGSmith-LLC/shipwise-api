<?php

use yii\db\Migration;

/**
 * Class m201007_214500_order_notes_6000
 */
class m201007_214500_order_notes_6000 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(6000)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%orders}}', 'notes', $this->string(1000)->null());
    }

}
