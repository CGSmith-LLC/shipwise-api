<?php

use yii\db\Migration;

/**
 * Class m230404_105609_remove_old_stripe_customer_ids
 */
class m230404_105609_remove_old_stripe_customer_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update("{{%customers}}", ['stripe_customer_id' => NULL]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
