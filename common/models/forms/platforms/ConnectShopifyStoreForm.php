<?php

namespace common\models\forms\platforms;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Expression;
use PHPShopify\Exception\SdkException;
use yii\web\ServerErrorHttpException;
use common\services\platforms\ShopifyService;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;
use common\models\Customer;

/**
 * Class ConnectShopifyStoreForm
 * @package common\models\forms\platforms
 */
class ConnectShopifyStoreForm extends Model
{
    public const SCENARIO_AUTH_REQUEST = 'scenarioAuthRequest';
    public const SCENARIO_SAVE_ACCESS_TOKEN = 'scenarioSaveAccessToken';

    public ?string $name = null;
    public ?string $url = null;
    public ?int $customer_id = null;
    public ?string $code = null;

    public function scenarios(): array
    {
        return [
            self::SCENARIO_AUTH_REQUEST => ['name', 'url', 'customer_id'],
            self::SCENARIO_SAVE_ACCESS_TOKEN => ['code'],
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'url', 'code'], 'filter', 'filter' => 'trim'],
            [['name', 'url', 'code'], 'string', 'max' => 128],
            /** @see https://www.regextester.com/104785 */
            ['url', 'match', 'pattern' => '/[^.\s]+\.myshopify\.com$/', 'message' => 'Invalid shop URL.'],

            [['name', 'url', 'customer_id'], 'required', 'on' => self::SCENARIO_AUTH_REQUEST],
            [['name'], 'validateShopName', 'on' => self::SCENARIO_AUTH_REQUEST],
            [['url'], 'validateShopUrl', 'on' => self::SCENARIO_AUTH_REQUEST],
            ['customer_id', 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id'], 'on' => self::SCENARIO_AUTH_REQUEST],

            [['url', 'code'], 'required', 'on' => self::SCENARIO_SAVE_ACCESS_TOKEN],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Shop Name',
            'url' => 'Shop URL',
            'customer_id' => 'Customer',
            'code' => 'Code',
        ];
    }

    public function validateShopName(): void
    {
        if (EcommerceIntegration::find()
            ->andWhere(new Expression('`meta` LIKE :name', [':name' => '%"' . $this->name . '"%']))
            ->andWhere(['platform_id' => EcommercePlatform::getShopifyObject()->id])
            ->exists())
        {
            $this->addError('name', 'Shop name already exists.');
        }
    }

    public function validateShopUrl(): void
    {
        if (EcommerceIntegration::find()
            ->andWhere(new Expression('`meta` LIKE :url', [':url' => '%"' . $this->url . '"%']))
            ->andWhere(['platform_id' => EcommercePlatform::getShopifyObject()->id])
            ->exists())
        {
            $this->addError('url', 'Shop URL already exists.');
        }
    }

    /**
     * @throws SdkException
     * @throws InvalidConfigException
     */
    public function auth(): void
    {
        $this->saveShopName();
        $this->saveCustomerId();

        // Step 1 - Send request to receive access token
        $shopifyService = new ShopifyService($this->url);
        $shopifyService->auth();
    }

    /**
     * @throws ServerErrorHttpException
     * @throws SdkException
     * @throws InvalidConfigException
     */
    public function saveAccessToken(): void
    {
        // Step 2 - Receive and save access token:
        $shopifyService = new ShopifyService($this->url);
        $shopifyService->accessToken(
            Yii::$app->session->get('shop_name', 'Shop Name'),
            Yii::$app->user->id,
            Yii::$app->session->get('customer_id'));
    }

    protected function saveShopName()
    {
        Yii::$app->session->set('shop_name', $this->name);
    }

    protected function saveCustomerId()
    {
        Yii::$app->session->set('customer_id', $this->customer_id);
    }
}
