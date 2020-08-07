<?php

use yii\db\Migration;

/**
 * Class m181031_030057_item_issue_validate
 */
class m181031_030057_item_issue_validate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(128)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_030057_item_issue_validate cannot be reverted.\n";

        return false;
    }

}
