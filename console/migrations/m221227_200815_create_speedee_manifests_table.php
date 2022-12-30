<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%speedee_manifests}}`.
 */
class m221227_200815_create_speedee_manifests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%speedee_manifests}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'customer_id' => $this->integer(),
            'ship_from_shipper_number' => $this->string(6),
            'ship_from_bane' => $this->string(),
            'ship_from_attention' => $this->string()->null(),
            'ship_from_address_1' => $this->string(),
            'ship_from_address_2' => $this->string()->null(),
            'ship_from_city' => $this->string(),
            'ship_from_zip' => $this->integer(5),
            'ship_from_country' => $this->string()->null(),
            'ship_from_email' => $this->string()->null(),
            'ship_from_phone' => $this->string(),
            'ship_to_import_field' => $this->string(), // Destination Company Identification Number/String
            'ship_to_shipper_number' => $this->string(6)->null(),
            'ship_to_name' => $this->string(),
            'ship_to_attention' => $this->string()->null(),
            'ship_to_address_1' => $this->string(),
            'ship_to_address_2' => $this->string()->null(),
            'ship_to_city' => $this->string(),
            'ship_to_country' => $this->string(),
            'ship_to_email' => $this->string()->null(),
            'ship_to_phone' => $this->string()->null(),
            'reference_1' => $this->string()->null(), // Additional Reference Field (Usually Invoice Number)
            'reference_2' => $this->string()->null(),
            'reference_3' => $this->string()->null(),
            'reference_4' => $this->string()->null(),
            'weight' => $this->integer(),
            'length' => $this->integer()->null(),
            'width' => $this->integer()->null(),
            'height' => $this->integer()->null(),
            'barcode' => $this->string(),
            'oversized' => $this->boolean()->defaultValue(false),
            'pickup_tag' => $this->boolean()->defaultValue(false),
            'aod' => $this->boolean()->defaultValue(false), // Package Requires an Acknowledgement of Delivery
            'aod_option' => $this->integer()->defaultValue(0), // See SDS spreadsheet cell N11
            'cod' => $this->boolean()->defaultValue(false),
            'cod_value' => $this->integer()->null(), // Amount to collect for COD (cents)
            'declared_value' => $this->integer(), // Declared value for insurance purposes (cents)
            'package_handling' => $this->integer(), // Package handling - flat amount
            'apply_package_handling' => $this->boolean()->defaultValue(false),
            'ship_date' => $this->date(), // Date package was picked up by driver; default is to use processing date
            'bill_to_shipper_number' => $this->string(6), // currently same as ship_to number
            'unboxed' => $this->boolean()->defaultValue(false),

            'manifest_filename' => $this->string()->null(),
            'is_manifest_sent'  => $this->boolean()->defaultValue(false),
            'checksum'          => $this->string()->null(),
            'created_at'        => $this->bigInteger(),
            'updated_at'        => $this->bigInteger(),
        ]);

        $this->addForeignKey(
            'fk-speedee-manifest-order-id',
            'speedee_manifests',
            'order_id',
            'orders',
            'id'
        );

        $this->addForeignKey(
            'fk-speedee-manifest-customer-id',
            'speedee_manifests',
            'customer_id',
            'customers',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-speedee-manifest-order-id', 'speedee_manifests');
        $this->dropForeignKey('fk-speedee-manifest-customer-id', 'speedee_manifests');

        $this->dropTable('{{%speedee_manifests}}');
    }
}
