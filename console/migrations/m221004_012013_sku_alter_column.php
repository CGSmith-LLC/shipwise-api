<?php

use yii\db\Migration;

/**
 * Class m221004_012013_sku_alter_column
 */
class m221004_012013_sku_alter_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sku}}', 'sku', $this->string(64));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sku}}', 'sku', $this->string(64));
    }

}
