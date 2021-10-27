<?php

use yii\db\Migration;

/**
 * Class m210918_202649_add_status_to_integration
 */
class m210918_202649_add_status_to_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%integration}}', 'status', $this->integer(2)->notNull()->defaultValue(0));
        $this->addColumn('{{%integration}}', 'created_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%integration}}', 'updated_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%integration}}', 'status');

        $this->dropColumn('{{%integration}}', 'created_date');
        $this->dropColumn('{{%integration}}', 'updated_date');
    }

}
