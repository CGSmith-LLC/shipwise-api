<?php


namespace common\services\fulfillment;


use common\models\FulfillmentMeta;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class ColdcoService extends BaseFulfillmentService
{
	private const META_URL = "url";
	private const META_TOKEN = "access_token";

	private Client $client;
	private string $access_token;

	public function applyMeta(array $metadata)
	{
		/** @var FulfillmentMeta $metadatum */
		foreach ($metadata as $metadatum) {
			switch ($metadatum->key) {
				case self::META_URL:
					$this->client = new Client(['baseUrl' => $metadatum->decryptedValue()]);
					break;
				case self::META_TOKEN:
					$this->access_token = $metadatum->decryptedValue();
					break;
			}
		}
	}

	public function makeCreateOrderRequest(array $requestInfo): bool
	{
		try {
			$response = $this->client->createRequest()
				->setMethod(method: 'POST')
				->setHeaders(['Authorization' => "BEARER {$this->access_token}"])
				->setContent(implode(array: $requestInfo))
				->send();
			if ($response->isOk) {
				var_dump($response);
				return true;
			}
		} catch (Exception | InvalidConfigException $e) {

		}

		return false;
	}
}