<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ecommerce_order_log}}`.
 */
class m230306_144546_create_ecommerce_order_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `ecommerce_order_log` (
              `id` int NOT NULL AUTO_INCREMENT,
              `platform_id` int NOT NULL,
              `integration_id` int NOT NULL,
              `original_order_id` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
              `internal_order_id` int DEFAULT NULL,
              `status` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `payload` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `meta` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL ,
                `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                `updated_date` DATETIME NULL DEFAULT NULL, 
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->addForeignKey(
            '{{%fk-ecommerce_order-platform_id}}',
            '{{%ecommerce_order_log}}',
            'platform_id',
            '{{%ecommerce_platform}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-ecommerce_order-integration_id}}',
            '{{%ecommerce_order_log}}',
            'integration_id',
            '{{%ecommerce_integration}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-ecommerce_order-internal_order_id}}',
            '{{%ecommerce_order_log}}',
            'internal_order_id',
            '{{%orders}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ecommerce_order_log}}');
    }
}
