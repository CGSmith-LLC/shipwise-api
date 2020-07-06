<?php

/**
 *  List of routes for API V1
 */

$version = "v1";

return [

    // API Documentation
    ""                   => "$version/default/doc", // Swagger UI with "Try it out" button
    "/$version/doc"      => "$version/default/doc", // Swagger UI with "Try it out" button
    "/$version/schema"   => "$version/default/schema", // Swagger JSON spec file

    // Orders
    [
        "class"         => 'yii\rest\UrlRule',
        "controller"    => ["$version/order"],
        "extraPatterns" => [
            "GET  {id}/items"    => "items",
            "GET  {id}/packages" => "packages",
            "GET  findbystatus"  => "findbystatus",
            "POST {id}/status"   => "status",
            "POST {id}/history"  => "history",
            "GET  find"          => "find",
        ],
    ],

    // Webhooks
    [
        "class"      => 'yii\rest\UrlRule',
        "controller" => ["$version/webhook"],
    ],

    // Customers
    [
        "class"      => 'yii\rest\UrlRule',
        "controller" => ["$version/customer"],
    ],

    // Inventory
    [
        "class"      => 'yii\rest\UrlRule',
        "controller" => ["$version/inventory"],
    ],

    // Shipping Rates
    "POST $version/shipping/rates" => "$version/shipping-rate/create",
];