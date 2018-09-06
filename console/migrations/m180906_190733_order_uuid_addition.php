<?php

use yii\db\Migration;

/**
 * Class m180906_190733_order_uuid_addition
 */
class m180906_190733_order_uuid_addition extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'uuid',
            $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'uuid');
    }
}
