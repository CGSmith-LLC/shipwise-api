<?php

/**
 *  List of routes for API V1
 */

$version = "v1";

return [

    // API Documentation
    ""                 => "$version/default/doc", // Swagger UI with "Try it out" button
    "/$version/doc"    => "$version/default/doc", // Swagger UI with "Try it out" button
    "/$version/schema" => "$version/default/schema", // Swagger JSON spec file

    // Orders
    [
        "class"         => 'yii\rest\UrlRule',
        "controller"    => ["$version/order"],
        "extraPatterns" => [
            "GET  {id}/items"   => "items",
            "GET  findbystatus" => "findbystatus",
            "GET  find"         => "find",
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
];