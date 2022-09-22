<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%behavior}}`.
 */
class m220921_233512_add_behavior_column_to_behavior_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%behavior}}', 'behavior', $this->string(128)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%behavior}}', 'behavior');
    }
}
