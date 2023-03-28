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
              UNIQUE KEY `shop` (`shop`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;           
        ");

        $this->addForeignKey(
            '{{%fk-shopify_app-customer_id}}',
            '{{%shopify_app}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'CASCADE'
        );

        $this->execute("
            CREATE TABLE `shopify_webhook` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL,
              `shopify_webhook_id` varchar(64) NOT NULL,
              `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;        
        ");

        $this->addForeignKey(
            '{{%fk-shopify_webhook-customer_id}}',
            '{{%shopify_webhook}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'CASCADE'
        );
    }
}
