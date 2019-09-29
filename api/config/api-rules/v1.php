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
	// @deprecated Not used since Basic Auth was implemented to replace API authentication.
	// "POST    $version/auth" => "$version/auth/login",
	// "DELETE  $version/auth" => "$version/auth/logout",

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

	// Customers
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => ["$version/customer"],
	],
];