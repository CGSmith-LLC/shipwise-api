<?php

use yii\db\Migration;

/**
 * Class m200316_021810_alter_packages_table_created_date_field
 */
class m200316_021810_alter_packages_table_created_date_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%packages}}',               'created_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('{{%package_items}}',          'created_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('{{%package_items_lot_info}}', 'created_date', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200316_021810_alter_packages_table_created_date_field cannot be reverted.\n";

        return false;
    }

}
