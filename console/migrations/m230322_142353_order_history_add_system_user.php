<?php

use yii\db\Migration;

/**
 * Class m230322_142353_order_history_add_system_user
 */
class m230322_142353_order_history_add_system_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `order_history` CHANGE `user_id` `user_id` INT NULL DEFAULT NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            ALTER TABLE `order_history` CHANGE `user_id` `user_id` INT NOT NULL;
        ");
    }
}
