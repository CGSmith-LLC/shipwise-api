<?php
return [

    'adminEmail'                    => 'admin@example.com',
    'supportEmail'                  => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'stripePublicKey'               => '1234',
    'stripePrivateKey'              => '1234',
    'shopifyPublicKey'              => '5678',
    'shopifyPrivateKey'             => '5678',
    /**
     * Shopify App Parameters and App Credentials
     */


    /**
     * Put here default values for when customers meta value does not exist
     * @see \common\components\CustomerSettings
     */
    'globalCustomerSettings'        => [

        // FedEx API credentials
        'fedex_api_account'  => '',
        'fedex_api_meter'    => '',
        'fedex_api_key'      => '',
        'fedex_api_password' => '',

        // UPS API credentials
        'ups_api_account'    => '',
        'ups_api_user_id'    => '',
        'ups_api_password'   => '',
        'ups_api_key'        => '',

        // Amazon MWS credentials
        'amazon_mws_seller_id'         => '',
        'amazon_mws_auth_token'        => '',
        'amazon_mws_aws_access_key_id' => '',
        'amazon_mws_aws_secret_key'    => '',

    ],

    /**
     * ShipWise invoicing info
     */
    'invoicing' => [
        'company' => 'ShipWise',
        'address' => 'PO Box 812',
        'city'    => 'East Troy',
        'state'   => 'WI',
        'zip'     => '53120',
        'email'   => 'support@getshipwise.com',
        'phone'   => '(262) 342-6638',
    ],

];
