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

    // SKU
    [
        "class"      => 'yii\rest\UrlRule',
        "controller" => ["$version/sku"],
        "extraPatterns" => [
            "GET  find" => "find",
        ],
    ],

    // Carrier
    [
        "class" => 'yii\rest\UrlRule',
        "controller" => ["$version/carrier"],
    ],

    // Statuses
    [
        "class" => 'yii\rest\UrlRule',
        "controller" => ["$version/status"],
    ],

    // Aliases
    "GET $version/aliases" => "$version/alias",

    "POST $version/inventory" => "$version/inventory/create",
    "POST $version/webhook" => "$version/webhook",
    "POST $version/webhook/import" => "$version/webhook/import",
    "POST $version/webhook/urban-smokehouse" => "$version/webhook/urban-smokehouse",
    "GET $version/inventory" => "$version/inventory",
    "DELETE $version/inventory/purge" => "$version/inventory/purge",

    // Shipping Rates
    "POST $version/shipping/rates" => "$version/shipping-rate/create",
];