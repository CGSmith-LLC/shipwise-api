<?php

namespace common\models\forms\platforms;

use Yii;
use yii\base\{InvalidConfigException, Model};
use yii\db\Expression;
use PHPShopify\Exception\SdkException;
use yii\web\ServerErrorHttpException;
use common\services\platforms\ShopifyService;
use common\models\{EcommerceIntegration, EcommercePlatform, Customer};

/**
 * Class ConnectShopifyStoreForm
 * @package common\models\forms\platforms
 */
class ConnectShopifyStoreForm extends Model
{
    public const SCENARIO_AUTH_REQUEST = 'scenarioAuthRequest';
    public const SCENARIO_SAVE_ACCESS_TOKEN = 'scenarioSaveAccessToken';

    public ?EcommerceIntegration $ecommerceIntegration = null;

    public ?string $name = null;
    public ?string $url = null;
    public string|array|null $order_statuses = null;
    public string|array|null $financial_statuses = null;
    public string|array|null $fulfillment_statuses = null;
    public ?int $customer_id = null;
    public ?string $code = null;

    public function scenarios(): array
    {
        return [
            self::SCENARIO_AUTH_REQUEST => ['name', 'url', 'order_statuses', 'financial_statuses', 'fulfillment_statuses', 'customer_id'],
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

            [['name', 'url', 'customer_id'], 'required',
                'on' => self::SCENARIO_AUTH_REQUEST],
            [['name'], 'validateShopName',
                'on' => self::SCENARIO_AUTH_REQUEST],
            [['url'], 'validateShopUrl',
                'on' => self::SCENARIO_AUTH_REQUEST],
            ['order_statuses', 'in', 'allowArray' => true,  'range' => array_keys(ShopifyService::$orderStatuses),
                'on' => self::SCENARIO_AUTH_REQUEST],
            ['financial_statuses', 'in', 'allowArray' => true,  'range' => array_keys(ShopifyService::$financialStatuses),
                'on' => self::SCENARIO_AUTH_REQUEST],
            ['fulfillment_statuses', 'in', 'allowArray' => true,  'range' => array_keys(ShopifyService::$fulfillmentStatuses),
                'on' => self::SCENARIO_AUTH_REQUEST],
            ['customer_id', 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id'],
                'on' => self::SCENARIO_AUTH_REQUEST],

            [['url', 'code'], 'required',
                'on' => self::SCENARIO_SAVE_ACCESS_TOKEN],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Shop Name',
            'url' => 'Shop URL',
            'order_statuses' => 'Order Statuses',
            'financial_statuses' => 'Financial Statuses',
            'fulfillment_statuses' => 'Fulfillment Statuses',
            'customer_id' => 'Customer',
            'code' => 'Code',
        ];
    }

    public function validateShopName(): void
    {
        $query = EcommerceIntegration::find()
            ->andWhere(new Expression('`meta` LIKE :name', [':name' => '%"' . $this->name . '"%']))
            ->andWhere(['platform_id' => EcommercePlatform::getShopifyObject()->id]);

        if ($this->ecommerceIntegration) {
            $query->andWhere('id != :id', ['id' => $this->ecommerceIntegration->id]);
        }

        if ($query->exists())
        {
            $this->addError('name', 'Shop name already exists.');
        }
    }

    public function validateShopUrl(): void
    {
        $query = EcommerceIntegration::find()
            ->andWhere(new Expression('`meta` LIKE :url', [':url' => '%"' . $this->url . '"%']))
            ->andWhere(['platform_id' => EcommercePlatform::getShopifyObject()->id]);

        if ($this->ecommerceIntegration) {
            $query->andWhere('id != :id', ['id' => $this->ecommerceIntegration->id]);
        }

        if ($query->exists())
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
        $this->saveDataForSecondStep();

        // Step 1 - Send request to receive access token
        $shopifyService = new ShopifyService($this->url);
        $shopifyService->auth();
    }

    /**
     * @throws ServerErrorHttpException
     * @throws SdkException
     * @throws InvalidConfigException
     */
    public function saveAccessToken(bool $addWebHookListeners = true): void
    {
        $data = unserialize(Yii::$app->session->get('shopify_connection_second_step'));

        if (isset($data['integration_id'])) {
            $this->ecommerceIntegration = EcommerceIntegration::findOne($data['integration_id']);
        }

        // Step 2 - Receive and save access token:
        $shopifyService = new ShopifyService($this->url);
        $shopifyService->accessToken($data, $this->ecommerceIntegration);

        // Add Webhook listeners:
        if ($addWebHookListeners) {
            $shopifyService->addWebhookListenersJob();
        }
    }

    protected function saveDataForSecondStep(): void
    {
        $data = [
            'shop_name' => $this->name,
            'customer_id' => $this->customer_id,
            'user_id' => Yii::$app->user->id,
            'order_statuses' => $this->order_statuses,
            'financial_statuses' => $this->financial_statuses,
            'fulfillment_statuses' => $this->fulfillment_statuses,
        ];

        if ($this->ecommerceIntegration) {
            $data['integration_id'] = $this->ecommerceIntegration->id;
        }

        Yii::$app->session->set('shopify_connection_second_step', serialize($data));
    }
}
