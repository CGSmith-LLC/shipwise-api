<?php

use yii\db\Migration;

/**
 * Class m200918_133216_substitute_items
 */
class m200918_133216_substitute_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sku}}', 'substitute_1', $this->string(64)->null());
        $this->addColumn('{{%sku}}', 'substitute_2', $this->string(64)->null());
        $this->addColumn('{{%sku}}', 'substitute_3', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sku}}', 'substitute_1');
        $this->dropColumn('{{%sku}}', 'substitute_2');
        $this->dropColumn('{{%sku}}', 'substitute_3');
    }

}
