<?php

use yii\db\Migration;

/**
 * Class m230401_140923_stripe_webhooks_table
 */
class m230401_140923_stripe_webhooks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `subscription_webhook` (
              `id` int NOT NULL AUTO_INCREMENT,
              `payment_method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `event` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `payload` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `meta` MEDIUMTEXT NULL DEFAULT NULL, 
                `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                `updated_date` DATETIME NULL DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            ALTER TABLE `subscription_webhook` 
                CHANGE `updated_date` `updated_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
        ");

        $this->execute("
            ALTER TABLE `subscription_webhook` ADD INDEX(`status`);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%subscription_webhook}}");
    }
}
