<?php

use yii\db\Migration;

/**
 * Class m220108_012009_integration_table_update
 */
class m220108_012009_integration_table_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%integration}}', 'ecommerce', 'platform');
        $this->dropColumn('{{%integration}}', 'fulfillment');
        $this->addColumn('{{%integration}}', 'type', $this->string(128)->defaultValue('ecommerce')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220108_012009_integration_table_update cannot be reverted.\n";

        return false;
    }

}
