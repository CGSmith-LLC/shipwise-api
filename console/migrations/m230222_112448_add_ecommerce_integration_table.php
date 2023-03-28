<?php

use yii\db\Migration;

/**
 * Class m230222_112448_add_ecommerce_integration_table
 */
class m230222_112448_add_ecommerce_integration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `ecommerce_integration` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `user_id` INT NOT NULL , 
                `customer_id` INT NOT NULL , 
                `platform_id` INT NOT NULL , 
                `status` TINYINT NOT NULL DEFAULT '0' ,  
                `meta` MEDIUMTEXT NULL DEFAULT NULL , 
                `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                `updated_date` DATETIME NULL DEFAULT NULL , 
                PRIMARY KEY (`id`)) ENGINE = InnoDB;
        ");

        $this->execute("
            ALTER TABLE `ecommerce_integration` 
                CHANGE `updated_date` `updated_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
        ");

        $this->execute("
            ALTER TABLE `ecommerce_integration` ADD INDEX(`status`);
        ");

        $this->addForeignKey(
            '{{%fk-ecommerce_integration-user_id}}',
            '{{%ecommerce_integration}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-ecommerce_integration-customer_id}}',
            '{{%ecommerce_integration}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-ecommerce_integration-platform_id}}',
            '{{%ecommerce_integration}}',
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
        $this->dropTable("{{%ecommerce_integration}}");
    }
}
