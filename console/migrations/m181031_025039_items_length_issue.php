<?php

use yii\db\Migration;

/**
 * Class m181031_025039_items_length_issue
 */
class m181031_025039_items_length_issue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(64)->null());
        $this->alterColumn('{{%items}}', 'sku', $this->string(64)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_025039_items_length_issue cannot be reverted.\n";

        return false;
    }

}
