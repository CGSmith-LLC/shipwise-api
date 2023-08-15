<?php

use yii\db\Migration;

/**
 * Class m230315_124607_order_attributes_field
 */
class m230315_124607_order_attributes_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'order_attributes',
            $this
                ->json()
                ->defaultValue(null)
                ->after('notes'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'order_attributes');
    }
}
