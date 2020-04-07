<?php

use yii\db\Migration;

/**
 * Handles the dropping of columns from table
 */
class m200313_011047_drop_columns_packaage_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%package_items}}', 'lot_number');
        $this->dropColumn('{{%package_items}}', 'serial_number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200313_011047_drop_columns_packaage_items_table cannot be reverted.\n";

        return false;
    }
}
