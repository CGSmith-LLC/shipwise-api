<?php

use yii\db\Migration;

/**
 * Class m230221_115138_remove_previous_shopify_tables
 */
class m230221_115138_remove_previous_shopify_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable("{{%shopify_app}}");
        $this->dropTable("{{%shopify_webhook}}");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
             CREATE TABLE `shopify_app` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL,
              `shop` varchar(128) NOT NULL,
              `scopes` varchar(128) NOT NULL,
              `access_token` varchar(128) DEFAULT NULL,
              `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `shop` (`shop`),
              KEY `customer_id` (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;           
        ");

        $this->execute("
            CREATE TABLE `shopify_webhook` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL,
              `shopify_webhook_id` varchar(64) NOT NULL,
              `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `customer_id` (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;        
        ");
    }
}
