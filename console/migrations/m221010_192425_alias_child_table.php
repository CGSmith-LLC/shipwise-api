<?php

use yii\db\Migration;

/**
 * Class m221010_192425_alias_child_table
 */
class m221010_192425_alias_child_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alias_children}}',[
            'id'          => $this->primaryKey(),
            'alias_id'    => $this->integer()->notNull(),
            'sku'         => $this->string(64)->notNull(),
            'name'        => $this->string(128)->notNull(),
            'quantity'    => $this->integer(11)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alias_children}}');
    }

}
