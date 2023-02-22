<?php

use yii\db\Migration;

/**
 * Class m230221_124952_create_table_ecommerce_platform
 */
class m230221_124952_create_table_ecommerce_platform extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `ecommerce_platform` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `name` VARCHAR(128) NOT NULL , 
                `status` TINYINT NOT NULL DEFAULT '1' , 
                `meta` MEDIUMTEXT NULL DEFAULT NULL , 
                `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                `updated_date` DATETIME NULL DEFAULT NULL , 
                PRIMARY KEY (`id`)) ENGINE = InnoDB;        
        ");

        $this->execute("
            ALTER TABLE `ecommerce_platform` CHANGE `updated_date` `updated_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%ecommerce_platform}}");
    }
}
