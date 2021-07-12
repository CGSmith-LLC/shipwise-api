<?php

use yii\db\Migration;

/**
 * Class m210712_132224_modify_item_column_length
 */
class m210712_132224_modify_item_column_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'notes', $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%items}}', 'notes', $this->string(64));
    }

}
