<?php

use yii\db\Migration;

/**
 * Class m200904_165806_add_item_alias_sku
 */
class m200904_165806_add_item_alias_sku extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%items}}', 'alias_quantity', $this->integer(11)->null());
        $this->addColumn('{{%items}}', 'alias_sku', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%items}}', 'alias_quantity');
        $this->dropColumn('{{%items}}', 'alias_sku');
    }

}
