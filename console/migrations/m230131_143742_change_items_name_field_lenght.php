<?php

use yii\db\Migration;

/**
 * Class m230131_143742_change_items_name_field_lenght
 */
class m230131_143742_change_items_name_field_lenght extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(128));
    }
}
