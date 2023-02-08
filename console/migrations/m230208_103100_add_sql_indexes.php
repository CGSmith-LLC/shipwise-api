<?php

use yii\db\Migration;

/**
 * Class m230208_103100_add_sql_indexes
 */
class m230208_103100_add_sql_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%alias_children}} ADD INDEX(`alias_id`);");
        $this->execute("ALTER TABLE {{%alias_parent}} ADD INDEX(`customer_id`);");

        $this->execute("ALTER TABLE {{%api_consumer}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%api_consumer}} ADD INDEX(`status`);");
        $this->execute("ALTER TABLE {{%api_consumer}} ADD INDEX(`superuser`);");
        $this->execute("ALTER TABLE {{%api_consumer}} ADD INDEX(`facility_id`);");

        $this->execute("ALTER TABLE {{%batch}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%batch_item}} ADD INDEX(`batch_id`);");
        $this->execute("ALTER TABLE {{%batch_item}} ADD INDEX(`order_id`);");

        $this->execute("ALTER TABLE {{%behavior}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%behavior}} ADD INDEX(`integration_id`);");
        $this->execute("ALTER TABLE {{%behavior}} ADD INDEX(`status`);");
        $this->execute("ALTER TABLE {{%behavior}} ADD INDEX(`order`);");
        $this->execute("ALTER TABLE {{%behavior}} ADD INDEX(`order`);");
        $this->execute("ALTER TABLE {{%behavior_meta}} ADD INDEX(`customer_id`);");

        $this->execute("ALTER TABLE {{%bulk_action}} ADD INDEX(`status`);");
        $this->execute("ALTER TABLE {{%bulk_action}} ADD INDEX(`print_mode`);");

        $this->execute("ALTER TABLE {{%fulfillment_meta}} ADD INDEX(`fulfillment_id`);");
        $this->execute("ALTER TABLE {{%fulfillment_service_mapping}} ADD INDEX(`service_id`);");
        $this->execute("ALTER TABLE {{%fulfillment_service_mapping}} ADD INDEX(`fulfillment_id`);");

        $this->execute("ALTER TABLE {{%integration}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%integration}} ADD INDEX(`status`);");
        $this->execute("ALTER TABLE {{%integration}} ADD INDEX(`webhooks_enabled`);");
        $this->execute("ALTER TABLE {{%integration_hookdeck}} ADD INDEX(`integration_id`);");
        $this->execute("ALTER TABLE {{%integration_meta}} ADD INDEX(`integration_id`);");
        $this->execute("ALTER TABLE {{%integration_webhook}} ADD INDEX(`integration_id`);");
        $this->execute("ALTER TABLE {{%integration_webhook}} ADD INDEX(`integration_hookdeck_id`);");

        $this->execute("ALTER TABLE {{%inventory}} ADD INDEX(`customer_id`);");

        $this->execute("ALTER TABLE {{%invoice}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%invoice}} ADD INDEX(`subscription_id`);");
        $this->execute("ALTER TABLE {{%invoice}} ADD INDEX(`status`);");
        $this->execute("ALTER TABLE {{%invoice_items}} ADD INDEX(`invoice_id`);");

        $this->execute("ALTER TABLE {{%one_time_charge}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%one_time_charge}} ADD INDEX(`added_to_invoice`);");
        $this->execute("ALTER TABLE {{%one_time_charge}} ADD INDEX(`charge_asap`);");

        $this->execute("ALTER TABLE {{%paymentmethod}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%paymentmethod}} ADD INDEX(`default`);");
        $this->execute("ALTER TABLE {{%payment_intent}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%payment_intent}} ADD INDEX(`payment_method_id`);");
        $this->execute("ALTER TABLE {{%payment_intent}} ADD INDEX(`invoice_id`);");
        $this->execute("ALTER TABLE {{%payment_intent}} ADD INDEX(`status`);");

        $this->execute("ALTER TABLE {{%scheduled_orders}} ADD INDEX(`order_id`);");
        $this->execute("ALTER TABLE {{%scheduled_orders}} ADD INDEX(`status_id`);");
        $this->execute("ALTER TABLE {{%scheduled_orders}} ADD INDEX(`customer_id`);");

        $this->execute("ALTER TABLE {{%shopify_app}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%shopify_webhook}} ADD INDEX(`customer_id`);");

        $this->execute("ALTER TABLE {{%sku}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%sku}} ADD INDEX(`sku`);");
        $this->execute("ALTER TABLE {{%sku}} ADD INDEX(`excluded`);");

        $this->execute("ALTER TABLE {{%country}} ADD INDEX(`abbreviation`);");
        $this->execute("ALTER TABLE {{%states}} ADD INDEX(`country`);");
        $this->execute("ALTER TABLE {{%states}} ADD INDEX(`abbreviation`);");

        $this->execute("ALTER TABLE {{%subscription}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%subscription_items}} ADD INDEX(`subscription_id`);");

        $this->execute("ALTER TABLE {{%tracking_info}} ADD INDEX(`carrier_id`);");
        $this->execute("ALTER TABLE {{%tracking_info}} ADD INDEX(`service_id`);");

        $this->execute("ALTER TABLE {{%user}} ADD INDEX(`customer_id`);");
        $this->execute("ALTER TABLE {{%user}} ADD INDEX(`facility_id`);");
        $this->execute("ALTER TABLE {{%user}} ADD INDEX(`type`);");

        $this->execute("ALTER TABLE {{%user_warehouse}} ADD INDEX(`warehouse_id`);");
        $this->execute("ALTER TABLE {{%user_warehouse}} ADD INDEX(`user_id`);");

        $this->execute("ALTER TABLE {{%webhook}} ADD INDEX(`active`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE {{%alias_children}} DROP INDEX `alias_id`;");
        $this->execute("ALTER TABLE {{%alias_parent}} DROP INDEX `customer_id`;");

        $this->execute("ALTER TABLE {{%api_consumer}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%api_consumer}} DROP INDEX `status`;");
        $this->execute("ALTER TABLE {{%api_consumer}} DROP INDEX `superuser`;");
        $this->execute("ALTER TABLE {{%api_consumer}} DROP INDEX `facility_id`;");

        $this->execute("ALTER TABLE {{%batch}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%batch_item}} DROP INDEX `batch_id`;");
        $this->execute("ALTER TABLE {{%batch_item}} DROP INDEX `order_id`;");

        $this->execute("ALTER TABLE {{%behavior}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%behavior}} DROP INDEX `integration_id`;");
        $this->execute("ALTER TABLE {{%behavior}} DROP INDEX `status`;");
        $this->execute("ALTER TABLE {{%behavior}} DROP INDEX `order`;");
        $this->execute("ALTER TABLE {{%behavior_meta}} DROP INDEX `customer_id`;");

        $this->execute("ALTER TABLE {{%bulk_action}} DROP INDEX `status`;");
        $this->execute("ALTER TABLE {{%bulk_action}} DROP INDEX `print_mode`;");

        $this->execute("ALTER TABLE {{%fulfillment_meta}} DROP INDEX `fulfillment_id`;");
        $this->execute("ALTER TABLE {{%fulfillment_service_mapping}} DROP INDEX `service_id`;");
        $this->execute("ALTER TABLE {{%fulfillment_service_mapping}} DROP INDEX `fulfillment_id`;");

        $this->execute("ALTER TABLE {{%integration}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%integration}} DROP INDEX `status`;");
        $this->execute("ALTER TABLE {{%integration}} DROP INDEX `webhooks_enabled`;");
        $this->execute("ALTER TABLE {{%integration_hookdeck}} DROP INDEX `integration_id`;");
        $this->execute("ALTER TABLE {{%integration_meta}} DROP INDEX `integration_id`;");
        $this->execute("ALTER TABLE {{%integration_webhook}} DROP INDEX `integration_id`;");
        $this->execute("ALTER TABLE {{%integration_webhook}} DROP INDEX `integration_hookdeck_id`;");

        $this->execute("ALTER TABLE {{%inventory}} DROP INDEX `customer_id`;");

        $this->execute("ALTER TABLE {{%invoice}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%invoice}} DROP INDEX `subscription_id`;");
        $this->execute("ALTER TABLE {{%invoice}} DROP INDEX `status`;");
        $this->execute("ALTER TABLE {{%invoice_items}} DROP INDEX `invoice_id`;");

        $this->execute("ALTER TABLE {{%one_time_charge}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%one_time_charge}} DROP INDEX `added_to_invoice`;");
        $this->execute("ALTER TABLE {{%one_time_charge}} DROP INDEX `charge_asap`;");

        $this->execute("ALTER TABLE {{%paymentmethod}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%paymentmethod}} DROP INDEX `default`;");
        $this->execute("ALTER TABLE {{%payment_intent}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%payment_intent}} DROP INDEX `payment_method_id`;");
        $this->execute("ALTER TABLE {{%payment_intent}} DROP INDEX `invoice_id`;");
        $this->execute("ALTER TABLE {{%payment_intent}} DROP INDEX `status`;");

        $this->execute("ALTER TABLE {{%scheduled_orders}} DROP INDEX `order_id`;");
        $this->execute("ALTER TABLE {{%scheduled_orders}} DROP INDEX `status_id`;");
        $this->execute("ALTER TABLE {{%scheduled_orders}} DROP INDEX `customer_id`;");

        $this->execute("ALTER TABLE {{%shopify_app}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%shopify_webhook}} DROP INDEX `customer_id`;");

        $this->execute("ALTER TABLE {{%sku}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%sku}} DROP INDEX `sku`;");
        $this->execute("ALTER TABLE {{%sku}} DROP INDEX `excluded`;");

        $this->execute("ALTER TABLE {{%country}} DROP INDEX `abbreviation`;");
        $this->execute("ALTER TABLE {{%states}} DROP INDEX `country`;");
        $this->execute("ALTER TABLE {{%states}} DROP INDEX `abbreviation`;");

        $this->execute("ALTER TABLE {{%subscription}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%subscription_items}} DROP INDEX `subscription_id`;");

        $this->execute("ALTER TABLE {{%tracking_info}} DROP INDEX `carrier_id`;");
        $this->execute("ALTER TABLE {{%tracking_info}} DROP INDEX `service_id`;");

        $this->execute("ALTER TABLE {{%user}} DROP INDEX `customer_id`;");
        $this->execute("ALTER TABLE {{%user}} DROP INDEX `facility_id`;");
        $this->execute("ALTER TABLE {{%user}} DROP INDEX `type`;");

        $this->execute("ALTER TABLE {{%user_warehouse}} DROP INDEX `warehouse_id`;");
        $this->execute("ALTER TABLE {{%user_warehouse}} DROP INDEX `user_id`;");

        $this->execute("ALTER TABLE {{%webhook}} DROP INDEX `active`;");
    }
}
