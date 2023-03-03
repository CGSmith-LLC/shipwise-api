<?php

namespace frontend\controllers;

use common\services\platforms\ShopifyService;
use frontend\models\Customer;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\AccessControl;
use Da\User\Filter\AccessRuleFilter;
use PHPShopify\Exception\SdkException;
use common\models\{EcommercePlatform, EcommerceIntegration};
use common\models\forms\platforms\ConnectShopifyStoreForm;
use yii\web\{NotFoundHttpException, ServerErrorHttpException};

class EcommerceIntegrationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => AccessRuleFilter::class,
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionTest()
    {
        $integrations = EcommerceIntegration::find()
            ->active()
            ->orderById()
            ->all();

        foreach ($integrations as $integration) {
            $accessToken = $integration->array_meta_data['access_token'];

            if ($accessToken) {
                $shopifyService = new ShopifyService($integration->array_meta_data['shop_url'], $integration);

                /**
                 * @see https://shopify.dev/docs/api/admin-rest/2022-10/resources/order#get-orders?status=any
                 */
                $params = [
                    'limit' => 250,
                ];

                if ($integration->isMetaKeyExistsAndNotEmpty('order_statuses')) {
                    $params['status'] = implode(',', $integration->array_meta_data['order_statuses']);
                }

                if ($integration->isMetaKeyExistsAndNotEmpty('financial_statuses')) {
                    $params['financial_status'] = implode(',', $integration->array_meta_data['financial_statuses']);
                }

                if ($integration->isMetaKeyExistsAndNotEmpty('fulfillment_statuses')) {
                    $params['fulfillment_status'] = implode(',', $integration->array_meta_data['fulfillment_statuses']);
                }

                $orders = $shopifyService->getOrdersList($params);

                echo '<pre>';
                print_r($orders);
                exit;

                foreach ($orders as $order) {
                    $shopifyService->parseRawOrderJob($order);
                }
            }
        }
    }

    /**
     * Lists all EcommerceIntegration models for the current user.
     * @return string
     */
    public function actionIndex(): string
    {
        $ecommerceIntegrations = EcommerceIntegration::find()
            ->with(['ecommercePlatform'])
            ->for(Yii::$app->user->id)
            ->orderById()
            ->all();

        return $this->render('index', [
            'models' => $ecommerceIntegrations,
        ]);
    }

    /**
     * Connects a new Shopify shop.
     * @throws SdkException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionShopify(): string|Response
    {
        $this->checkEcommercePlatformByName(EcommercePlatform::SHOPIFY_PLATFORM_NAME);

        if (Yii::$app->request->isPost) {
            // Step 1 - Send request to receive access token:
            $model = new ConnectShopifyStoreForm([
                'scenario' => ConnectShopifyStoreForm::SCENARIO_AUTH_REQUEST
            ]);
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {
                $model->auth();
            }
        } elseif (Yii::$app->request->get('code')) {
            // Step 2 - Receive and save access token:
            $model = new ConnectShopifyStoreForm([
                'scenario' => ConnectShopifyStoreForm::SCENARIO_SAVE_ACCESS_TOKEN,
                'url' => Yii::$app->request->get('shop'),
                'code' => Yii::$app->request->get('code')
            ]);

            if ($model->validate()) {
                $model->saveAccessToken();

                Yii::$app->session->setFlash('success', 'Shopify shop has been connected.');
                return $this->redirect(['index']);
            }
        } else {
            $model = new ConnectShopifyStoreForm();
        }

        return $this->render('shopify', [
            'model' => $model,
            'customersList' => $this->getCustomersList(),
        ]);
    }

    /**
     * Disconnects a needed EcommerceIntegration model.
     * @throws NotFoundHttpException
     */
    public function actionDisconnect(int $id): Response
    {
        $ecommerceIntegration = $this->getEcommerceIntegrationById($id);
        $ecommerceIntegration->disconnect();

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been disconnected.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Makes a needed EcommerceIntegration model paused.
     * @throws NotFoundHttpException
     */
    public function actionPause(int $id): Response
    {
        $ecommerceIntegration = $this->getEcommerceIntegrationById($id);
        $ecommerceIntegration->pause();

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been paused.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Makes a needed EcommerceIntegration model active again.
     * @throws NotFoundHttpException
     */
    public function actionResume(int $id): Response
    {
        $ecommerceIntegration = $this->getEcommerceIntegrationById($id);
        $ecommerceIntegration->resume();

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been resumed.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Returns a needed EcommerceIntegration model by its ID.
     * @throws NotFoundHttpException
     */
    protected function getEcommerceIntegrationById(int $id): EcommerceIntegration
    {
        $model = EcommerceIntegration::find()
            ->for(Yii::$app->user->id)
            ->where(['id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Ecommerce integration does not exist.');
        }

        /**
         * @var $model EcommerceIntegration
         */
        return $model;
    }

    protected function getCustomersList(): array
    {
        if (Yii::$app->user->identity->isAdmin) {
            $data = Customer::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();
        } else {
            $data = Customer::find()
                ->where("`id` IN(SELECT DISTINCT(`customer_id`) FROM `user_customer` WHERE `user_id` = :user_id)", [
                    'user_id' => Yii::$app->user->id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();
        }

        return ArrayHelper::map($data, 'id','name');
    }

    /**
     * Checks a needed EcommercePlatform model by the provided name. It must exist and be active.
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function checkEcommercePlatformByName(string $name): void
    {
        /**
         * @var EcommercePlatform $model
         */
        $model = EcommercePlatform::find()
            ->where(['name' => $name])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Ecommerce platform does not exist.');
        }

        if (!$model->isActive()) {
            throw new ServerErrorHttpException('Ecommerce platform is not active.');
        }
    }
}
