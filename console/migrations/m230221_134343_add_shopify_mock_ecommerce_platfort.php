<?php

use yii\db\Migration;
use common\models\EcommercePlatform;

/**
 * Class m230221_134343_add_shopify_mock_ecommerce_platfort
 */
class m230221_134343_add_shopify_mock_ecommerce_platfort extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $ecommercePlatform = new EcommercePlatform();
        $ecommercePlatform->name = EcommercePlatform::SHOPIFY_PLATFORM_NAME;
        $ecommercePlatform->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $ecommercePlatform = EcommercePlatform::find()
            ->where(['name' => EcommercePlatform::SHOPIFY_PLATFORM_NAME])
            ->one();

        if ($ecommercePlatform) {
            $ecommercePlatform->delete();
        }
    }
}
