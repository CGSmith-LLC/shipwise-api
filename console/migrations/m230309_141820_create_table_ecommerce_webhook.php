<?php

use yii\db\Migration;

/**
 * Class m230309_141820_create_table_ecommerce_webhook
 */
class m230309_141820_create_table_ecommerce_webhook extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `ecommerce_webhook` (
              `id` int NOT NULL AUTO_INCREMENT,
              `platform_id` int NOT NULL,
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
            ALTER TABLE `ecommerce_webhook` 
                CHANGE `updated_date` `updated_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
        ");

        $this->execute("
            ALTER TABLE `ecommerce_webhook` ADD INDEX(`status`);
        ");

        $this->addForeignKey(
            '{{%fk-ecommerce_webhook-platform_id}}',
            '{{%ecommerce_webhook}}',
            'platform_id',
            '{{%ecommerce_platform}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%ecommerce_webhook}}");
    }
}
