<?php

namespace common\models\base;

use yii\base\Model;

/**
 * Shipment model.
 *
 * @property int    $customer_id                Customer ID
 * @property string $package_type               Packaging type
 * @property string $shipment_date              The ship datetime
 * @property string $weight_units               Weight units. KG, LB
 * @property string $dim_units                  Dimension units. CM, IN
 * @property string $sender_contact             Sender contact name
 * @property string $sender_company             Sender company name
 * @property string $sender_address1            Sender address line 1
 * @property string $sender_address2            Sender address line 2
 * @property string $sender_city                Sender city
 * @property string $sender_state               Sender state / province
 * @property string $sender_postal_code         Sender postal code / ZIP
 * @property int    $sender_country             Sender country ISO code
 * @property string $sender_phone               Sender hone number
 * @property string $sender_phone_ext           Sender phone extension
 * @property string $sender_email               Sender email
 * @property string $sender_tax_id              Sender tax id information
 * @property int    $sender_address_id          Sender Address ID reference if any
 * @property int    $sender_is_residential      If sender is a residential address
 * @property string $recipient_contact          Recipient contact name
 * @property string $recipient_company          Recipient company name
 * @property string $recipient_address1         Recipient address line 1
 * @property string $recipient_address2         Recipient address line 2
 * @property string $recipient_city             Recipient city
 * @property string $recipient_state            Recipient state / province
 * @property string $recipient_postal_code      Recipient postal code / ZIP
 * @property int    $recipient_country          Recipient country ISO code
 * @property string $recipient_phone            Recipient phone number
 * @property string $recipient_phone_ext        Recipient phone extension
 * @property string $recipient_email            Recipient email
 * @property string $recipient_tax_id           Recipient tax id information
 * @property int    $recipient_is_residential   If recipient is a residential address
 * @property int    $recipient_address_id       Recipient Address ID reference if any
 * @property string $currency                   The currency code
 * @property string $insurance_amount           The insurance amount
 * @property string $package_contents           Package contents: NON_DOCUMENTS (parcel), DOCUMENTS
 * @property string $package_doc_type           Package document type when package_contents is DOCUMENTS
 * @property string $type_of_document_other     Type of document when package_doc_type is other
 * @property int    $is_saturday_delivery       Whether special service "saturday delivery" was requested
 * @property int    $is_dry_ice                 Whether special service "dry ice" was requested
 * @property int    $is_dangerous_goods         Whether special service "dangerous goods" was requested
 * @property int    $is_flat_rate               Carrier's flat rate option. Ex. "FedEx One Rate"
 * @property string $bill_transport_to          Bill transport to SENDER, RECIPIENT or THIRD_PARTY
 * @property string $bill_transport_account_num The billing account number when bill_transport_to value is THIRD_PARTY
 * @property string $bill_duties_to             Bill duties (customs) to SENDER,
 *           RECIPIENT or THIRD_PARTY
 * @property string $bill_duties_account_num    The billing account number when bill_duties_to value is THIRD_PARTY
 */
class BaseShipment extends Model
{

    public $customer_id;
    public $package_type;
    public $shipment_date;
    public $weight_units;
    public $dim_units;
    public $sender_contact;
    public $sender_company;
    public $sender_address1;
    public $sender_address2;
    public $sender_city;
    public $sender_state;
    public $sender_postal_code;
    public $sender_country;
    public $sender_phone;
    public $sender_phone_ext;
    public $sender_email;
    public $sender_tax_id;
    public $sender_address_id;
    public $sender_is_residential;
    public $recipient_contact;
    public $recipient_company;
    public $recipient_address1;
    public $recipient_address2;
    public $recipient_city;
    public $recipient_state;
    public $recipient_postal_code;
    public $recipient_country;
    public $recipient_phone;
    public $recipient_phone_ext;
    public $recipient_email;
    public $recipient_tax_id;
    public $recipient_is_residential;
    public $recipient_address_id;
    public $currency;
    public $insurance_amount;
    public $is_saturday_delivery;
    public $package_contents = 'NON_DOCUMENTS';
    public $package_doc_type;
    public $type_of_document_other;
    public $is_dry_ice;
    public $is_dangerous_goods;
    public $is_flat_rate;
    public $bill_transport_to = 'SENDER';
    public $bill_transport_account_num;
    public $bill_duties_to = 'RECIPIENT';
    public $bill_duties_account_num;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'customer_id',
                    'shipment_status_id',
                    'sender_is_residential',
                    'sender_address_id',
                    'recipient_is_residential',
                    'recipient_address_id',
                    'is_saturday_delivery',
                    'is_dry_ice',
                    'is_dangerous_goods',
                    'is_flat_rate',
                ],
                'integer',
            ],
            [['shipment_date'], 'safe'],
            [
                [
                    'sender_company',
                    'sender_address1',
                    'sender_city',
                    'sender_country',
                    'recipient_company',
                    'recipient_address1',
                    'recipient_city',
                    'recipient_country',
                    'currency',
                ],
                'required',
            ],
            [['insurance_amount', 'dry_ice_weight'], 'number'],
            [['weight_units', 'dim_units', 'sender_country', 'recipient_country'], 'string', 'max' => 2],
            [
                ['bill_transport_account_num', 'bill_duties_account_num', 'type_of_document_other',],
                'string',
                'max' => 64,
            ],
            [
                [
                    'sender_contact',
                    'sender_company',
                    'sender_city',
                    'sender_email',
                    'recipient_contact',
                    'recipient_company',
                    'recipient_city',
                    'recipient_email',
                    'dangerous_goods_types',
                    'package_type',
                ],
                'string',
                'max' => 80,
            ],
            [['sender_email', 'recipient_email'], 'email'],
            [
                ['sender_address1', 'sender_address2', 'recipient_address1', 'recipient_address2'],
                'string',
                'max' => 120,
            ],
            [['sender_state', 'sender_postal_code', 'recipient_state', 'recipient_postal_code'], 'string', 'max' => 20],
            [
                [
                    'sender_phone',
                    'recipient_phone',
                    'bill_transport_to',
                    'bill_duties_to',
                    'package_contents',
                ],
                'string',
                'max' => 32,
            ],
            [['sender_phone_ext', 'recipient_phone_ext'], 'string', 'max' => 10],
            [['sender_tax_id', 'recipient_tax_id'], 'string', 'max' => 30],
            [['currency'], 'string', 'max' => 3],
            [['package_doc_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carrier'                  => 'Carrier',
            'service'                  => 'Service',
            'service.name'             => 'Service',
            'package_type'             => 'Packaging',
            'packageType.name'         => 'Packaging',
            'shipment_status_id'       => 'Shipment Status',
            'status.name'              => 'Status',
            'shipment_date'            => 'Shipment Date',
            'weight_units'             => 'Weight Units',
            'dim_units'                => 'Dim Units',
            'sender_contact'           => 'Sender Contact',
            'sender_company'           => 'Sender Company',
            'sender_address1'          => 'Sender Address 1',
            'sender_address2'          => 'Sender Address 2',
            'sender_city'              => 'Sender City',
            'sender_state'             => 'Sender State / Province',
            'sender_postal_code'       => 'Sender Postal Code',
            'sender_country'           => 'Sender Country',
            'sender_phone'             => 'Sender Phone',
            'sender_phone_ext'         => 'Sender Phone Ext',
            'sender_email'             => 'Sender Email',
            'sender_tax_id'            => 'Sender Tax ID',
            'sender_address_id'        => 'Sender Address ID',
            'sender_is_residential'    => 'Sender Is Residential',
            'recipient_contact'        => 'Recipient Contact',
            'recipient_company'        => 'Recipient Company',
            'recipient_address1'       => 'Recipient Address 1',
            'recipient_address2'       => 'Recipient Address 2',
            'recipient_city'           => 'Recipient City',
            'recipient_state'          => 'Recipient State / Province',
            'recipient_postal_code'    => 'Recipient Postal Code',
            'recipient_country'        => 'Recipient Country',
            'recipient_phone'          => 'Recipient Phone',
            'recipient_phone_ext'      => 'Recipient Phone Ext',
            'recipient_email'          => 'Recipient Email',
            'recipient_tax_id'         => 'Recipient Tax ID',
            'recipient_is_residential' => 'Recipient Is Residential',
            'recipient_address_id'     => 'Recipient Address ID',
            'currency'                 => 'Currency',
            'insurance_amount'         => 'Insurance Amount',
            'is_saturday_delivery'     => 'Is Saturday Delivery',
            'is_dry_ice'               => 'Is Dry Ice',
            'dry_ice_weight'           => 'Dry Ice Weight',
            'is_dangerous_goods'       => 'Is Dangerous Goods',
            'dangerous_goods_types'    => 'Dangerous Goods Types',
            'is_flat_rate'             => 'Is Flat Rate',
        ];
    }
}
