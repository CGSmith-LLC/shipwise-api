<?php

use yii\db\Migration;

/**
 * Class m230404_111402_subscription_history_table
 */
class m230404_111402_subscription_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `subscription_history` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL,
              `payment_method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `payment_method_subscription_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
              `is_active` tinyint(1) NOT NULL DEFAULT '0',
              `is_trial` tinyint(1) NOT NULL,
              `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `paid_amount` float(10,2) NOT NULL,
              `paid_currency` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
              `plan_interval` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
              `plan_period_start` datetime NOT NULL,
              `plan_period_end` datetime NOT NULL,
              `meta` mediumtext COLLATE utf8mb4_unicode_ci,
               `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
               `updated_date` DATETIME NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `is_active` (`is_active`),
              KEY `payment_method` (`payment_method`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            ALTER TABLE `subscription_history` ADD `plan_name` VARCHAR(128) NOT NULL AFTER `paid_currency`;
        ");

        $this->execute("
            ALTER TABLE `subscription_history` 
                CHANGE `updated_date` `updated_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
        ");

        $this->addForeignKey(
            '{{%fk-subscription_history-customer_id}}',
            '{{%subscription_history}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%subscription_history}}");
    }
}
