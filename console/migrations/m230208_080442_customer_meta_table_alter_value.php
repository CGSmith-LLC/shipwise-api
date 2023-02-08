<?php

use yii\db\Migration;

/**
 * Class m191023_014053_customer_meta_data
 */
class m230208_080442_customer_meta_table_alter_value extends Migration
{
    /** {@inheritdoc} */
    public function safeUp()
    {
        $this->alterColumn('{{%customers_meta}}', 'value', $this->text());
    }

    /** {@inheritdoc} */
    public function safeDown()
    {
        echo "m230208_080442_customer_meta_table_alter_value cannot be reverted.\n";

        return false;
    }
}
