<?php

use yii\db\Migration;

/**
 * Class m201220_195434_add_uuid_on_package_items
 */
class m201220_195434_add_uuid_on_package_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%package_items}}', 'uuid', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%package_items}}', 'uuid');
    }

}
