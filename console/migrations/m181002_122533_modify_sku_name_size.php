<?php

use yii\db\Migration;

/**
 * Class m181002_122533_modify_sku_name_size
 */
class m181002_122533_modify_sku_name_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%items}}', 'name', $this->string(64));
    }
}
