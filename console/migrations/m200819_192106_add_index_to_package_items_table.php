<?php

use yii\db\Migration;

/**
 * Class m200819_192106_add_index_to_package_items_table
 */
class m200819_192106_add_index_to_package_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createIndex('package_id_idx', '{{%package_items}}', 'package_id');
      $this->createIndex('order_id_idx', '{{%package_items}}', 'order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex('package_id_idx', '{{%package_items}}');
      $this->dropIndex('order_id_idx', '{{%package_items}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200819_192106_add_index_to_package_items_table cannot be reverted.\n";

        return false;
    }
    */
}
