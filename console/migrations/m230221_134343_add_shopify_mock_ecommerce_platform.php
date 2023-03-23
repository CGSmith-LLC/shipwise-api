<?php

use yii\db\Migration;

/**
 * Class m230221_134343_add_shopify_mock_ecommerce_platform
 */
class m230221_134343_add_shopify_mock_ecommerce_platform extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%ecommerce_platform}}', [
            'name' => 'Shopify'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%ecommerce_platform}}', [
            'name' => 'Shopify'
        ]);
    }
}
