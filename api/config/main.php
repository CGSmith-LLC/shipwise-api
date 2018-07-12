<?php
$params = yii\helpers\ArrayHelper::merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

$routes = yii\helpers\ArrayHelper::merge(
	[],
	require(__DIR__ . '/api-rules/v1.php')
);

return [
	'id'                  => 'app-api',
	'basePath'            => dirname(__DIR__),
	'controllerNamespace' => 'api\controllers',
	'modules'             => [
		'v1' => [
			'class' => 'api\modules\v1\Module',
		],
	],
	'bootstrap'           => [
		'log' => [
			"class"   => '\yii\filters\ContentNegotiator',
			"formats" => [
				//  comment next line to use GII
				'application/json' => \yii\web\Response::FORMAT_JSON,
			],
		],
	],
	'components'          => [
		'request' => [
			"enableCookieValidation" => false,
			"enableCsrfValidation"   => false,
			"parsers"                => [
				"application/json" => 'yii\web\JsonParser',
			],
		],

		'user' => [
			'identityClass'   => 'api\modules\v1\models\ApiUser',
			'enableSession'   => false,
			'enableAutoLogin' => false,
			'loginUrl'        => null,
		],

		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets'    => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],

		'urlManager' => [
			'class'               => 'yii\web\UrlManager',
			'enablePrettyUrl'     => true,
			'showScriptName'      => false,
			'enableStrictParsing' => true,
			'rules'               => $routes,
		],

		/*'formatter' => [
			'numberFormatterOptions' => [
				NumberFormatter::MIN_FRACTION_DIGITS => 0,
				NumberFormatter::MAX_FRACTION_DIGITS => 2,
			],
		],*/

		// Comment the whole response block to use gii
		"response"   => [
			"class"         => '\yii\web\Response',
			"on beforeSend" => function ($event) {
				//  get sender object
				$response = $event->sender;

				if (!is_null($response->data)) {
					//  if there is an error, format data correctly
					if (!is_null(Yii::$app->getErrorHandler()->exception)) {
						$response->data = [
							"code"    => $response->statusCode,
							"message" => $response->data["message"],
						];
					} else if (!$response->getIsSuccessful()) {
						$data = ["code" => $response->statusCode];

						if (isset($response->data["errors"])) {
							$data["errors"] = $response->data["errors"];
						}

						if (isset($response->data["message"])) {
							$data["message"] = $response->data["message"];
						}

						$response->data = $data;
					}
				}
			},
		],
	],
	'params'              => $params,
];