<?php

use yii\db\Migration;

/**
 * Class m230329_114259_remove_old_subscription_logic
 */
class m230329_114259_remove_old_subscription_logic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable("{{%subscription}}");
        $this->dropTable("{{%subscription_items}}");
        $this->dropTable("{{%invoice}}");
        $this->dropTable("{{%invoice_items}}");
        $this->dropTable("{{%one_time_charge}}");
        $this->dropTable("{{%paymentmethod}}");
        $this->dropTable("{{%payment_intent}}");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            CREATE TABLE `subscription` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL COMMENT 'Reference to customer',
              `next_invoice` date NOT NULL COMMENT 'The Next Date to generate an invoice',
              `months_to_recur` int NOT NULL COMMENT 'How many months will be used to calculate the next invoice',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `subscription_items` (
              `id` int NOT NULL AUTO_INCREMENT,
              `subscription_id` int NOT NULL COMMENT 'Reference to subscriptions',
              `name` varchar(128) NOT NULL,
              `amount` int NOT NULL COMMENT 'amount in cents',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `invoice` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL COMMENT 'Reference to customer',
              `subscription_id` int NOT NULL COMMENT 'Reference to Subscription ID',
              `customer_name` varchar(64) NOT NULL COMMENT 'Customer Name',
              `amount` int NOT NULL COMMENT 'Total in Cents',
              `balance` int NOT NULL COMMENT 'Balance Due in Cents',
              `due_date` date NOT NULL COMMENT 'Due Date',
              `stripe_charge_id` char(128) DEFAULT NULL COMMENT 'stripe charge id',
              `status` int NOT NULL COMMENT 'Status of transaction',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `invoice_items` (
              `id` int NOT NULL AUTO_INCREMENT,
              `invoice_id` int NOT NULL COMMENT 'Reference to invoice table',
              `name` varchar(128) NOT NULL,
              `amount` int NOT NULL COMMENT 'cents',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `one_time_charge` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL COMMENT 'Reference to customer',
              `name` varchar(128) NOT NULL,
              `amount` int NOT NULL COMMENT 'In cents',
              `added_to_invoice` tinyint(1) NOT NULL DEFAULT '0',
              `charge_asap` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `payment_intent` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int NOT NULL COMMENT 'Reference to customer',
              `payment_method_id` int NOT NULL COMMENT 'Reference to payment method table',
              `invoice_id` int NOT NULL,
              `stripe_payment_intent_id` char(128) DEFAULT NULL COMMENT 'stripe payment intent id',
              `amount` int NOT NULL COMMENT 'Total in Cents',
              `status` varchar(64) NOT NULL COMMENT 'Stripe Status of Payment Intent',
              `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE `paymentmethod` (
              `id` int NOT NULL AUTO_INCREMENT,
              `customer_id` int DEFAULT NULL COMMENT 'Reference to customer',
              `stripe_payment_method_id` char(128) DEFAULT NULL,
              `default` tinyint(1) NOT NULL COMMENT 'Is this the customer''s default payment method?',
              `brand` varchar(64) DEFAULT NULL,
              `lastfour` varchar(64) DEFAULT NULL,
              `expiration` varchar(64) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
