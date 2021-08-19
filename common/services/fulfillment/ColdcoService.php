<?php


namespace common\services\fulfillment;


use common\models\FulfillmentMeta;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class ColdcoService extends BaseFulfillmentService
{
	private const META_URL = "url";
	private const META_CLIENT_ID = "client_id";
	private const META_SECRET = "secret";
	private const META_3PL_KEY = "key";
	private const META_3PL_ID = "3pl_id";
	private const META_LOGIN = "username";
	private const META_TOKEN = "auth_token";
	private const META_TOKEN_EXPIRE = "auth_token_expiration";

	private const AUTH_URI = "AuthServer/api/Token";

	private Client $client;
	private string $clientid;
	private string $clientsecret;
	private string $user_login;
	private ?string $access_token = null;

	/**
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws InvalidConfigException
	 * @throws Exception
	 * @throws \yii\db\StaleObjectException
	 */
	public function applyMeta(array $metadata)
	{
		/** @var FulfillmentMeta $metadatum */
		foreach ($metadata as $metadatum) {
			switch ($metadatum->key) {
				/** Set URL */
				case self::META_URL:
					$this->client = new Client(['baseUrl' => $metadatum->decryptedValue()]);
					break;
				/** Set Client ID */
				case self::META_CLIENT_ID:
					$this->clientid = $metadatum->decryptedValue();
					break;
				/** Set Secret Key */
				case self::META_SECRET:
					$this->clientsecret = $metadatum->decryptedValue();
					break;
				/** Set User Login Info */
				case self::META_LOGIN:
					$this->user_login = $metadatum->decryptedValue();
					break;
				/** Check if token is expired. If so, generate a new one and set it. Update expiration time & token in DB */
				case self::META_TOKEN_EXPIRE:
					$response = null;
					if($metadatum->decryptedValue() < time()) {
						$response = $this->generateNewAccessToken();

						$transaction = \Yii::$app->db->beginTransaction();

						try {
							$metadatum->updateMeta(newval: $response['expire_time']);
							FulfillmentMeta::findOne(['fulfillment_id' => $metadatum->fulfillment_id, 'key' => self::META_TOKEN])
								->updateMeta(newval: $response['access_token']);
							$transaction->commit();
						} catch (\yii\db\Exception $e) {
							$transaction->rollBack();
							throw new \yii\base\Exception(message: 'Token Update Error. ' . $e->getMessage());
						}

					}
					break;
				/** Check if token has not been set yet. If so, get the token. */
				case self::META_TOKEN:
					if(is_null($this->access_token)) {
						$this->access_token = $metadatum->decryptedValue();
					}
					break;
			}
		}
	}

	/**
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function makeCreateOrderRequest(array $requestInfo): bool
	{
		$rqInfo = Json::encode($requestInfo);

		$response = $this->client->createRequest()
			->setMethod(method: 'POST')
			->setUrl(url: '/orders')
			->setHeaders([
				'Authorization' => "BEARER {$this->access_token}",
				'Content-Type' => 'application/json; charset=utf-8',
				'Accept' => 'application/hal+json',
				'Content-Length' => strlen($rqInfo),
			])
			->setContent($rqInfo)
			->send();

		if ($response->isOk) {
			return true;
		}

		throw new Exception(message: "Response Not OK." . PHP_EOL . $response->toString());
	}

	/**
	 * @throws InvalidConfigException|Exception
	 */
	public function generateNewAccessToken()
	{
		/**
		 * We need to authenticate with 3PL
		 * @link http://api.3plcentral.com/rels/auth
		 */

		$json = Json::encode([
			'grant_type' => 'client_credentials',
			'user_login' => $this->user_login,
		]);

		$response = $this->client->createRequest()
			->setMethod(method: 'POST')
			->setUrl(url: self::AUTH_URI)
			->setHeaders(headers: [
				'Authorization' => 'Basic ' . base64_encode($this->clientid . ':' . $this->clientsecret),
				'Content-Type' => 'application/json; charset=utf-8',
				'Accept' => 'application/json',
				'Content-Length' => strlen($json),
			])
			->setContent($json)
			->send();

		$body = Json::decode($response->getContent());

		try {
			$this->access_token = $body['access_token'];
		} catch (ErrorException $e) {
			die('Error happened in ColdcoService.php::generateNewAccessToken' . $e->getMessage());
		}

		return [
			'access_token' => $body['access_token'],
			'expire_time' => $body['expires_in'] + time(),
		];
	}
}