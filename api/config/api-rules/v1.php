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

	// Authentication

	"POST    $version/auth" => "$version/auth/login",
	"DELETE  $version/auth" => "$version/auth/logout",

	// Orders
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => ["$version/order"],
	],
	// @todo Add two missing endpoints for Orders:
	// `/orders/{orderID}/items` - Fetch order items
	// `/orders/findByStatus` - Fetch orders by status

	// Customers
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => ["$version/customer"],
	],
];