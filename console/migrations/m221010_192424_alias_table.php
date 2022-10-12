<?php

use yii\db\Migration;

/**
 * Class m221010_192424_alias_table
 */
class m221010_192424_alias_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alias_parent}}',[
            'id'          => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'sku'       => $this->string(64)->notNull(),
            'name'        => $this->string(128)->notNull(),
            'active'      => $this->boolean()->notNull()->defaultValue(1),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alias_parent}}');
    }

}
