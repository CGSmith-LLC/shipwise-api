<?php
return [

    'adminEmail'                    => 'admin@example.com',
    'supportEmail'                  => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,


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
    ],
];
