<?php

namespace frontend\controllers;

use common\services\platforms\ShopifyService;
use frontend\models\Customer;
use Yii;
use yii\base\InvalidConfigException;
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
     * @param int|null $id Reconnect a shop by ID.
     * @return string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws SdkException
     * @throws ServerErrorHttpException
     */
    public function actionShopify(?int $id = null): string|Response
    {
        $this->checkEcommercePlatformByName(EcommercePlatform::SHOPIFY_PLATFORM_NAME);

        $model = new ConnectShopifyStoreForm();
        $model->scenario = ConnectShopifyStoreForm::SCENARIO_AUTH_REQUEST;

        if ($id) { // Edit existing model (Reconnect action):
            $ecommerceIntegration = $this->getEcommerceIntegrationById($id);
            $model->ecommerceIntegration = $ecommerceIntegration;

            $model->setAttributes([
                'name' => $ecommerceIntegration->array_meta_data['shop_name'],
                'url' => $ecommerceIntegration->array_meta_data['shop_url'],
                'customer_id' => $ecommerceIntegration->customer_id,
                'order_statuses' => $ecommerceIntegration->array_meta_data['order_statuses'],
                'financial_statuses' => $ecommerceIntegration->array_meta_data['financial_statuses'],
                'fulfillment_statuses' => $ecommerceIntegration->array_meta_data['fulfillment_statuses'],
            ]);
        }

        if (Yii::$app->request->isPost) {
            // Step 1 - Send request to receive access token:
            $model->scenario = ConnectShopifyStoreForm::SCENARIO_AUTH_REQUEST;
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {
                $model->auth();
            }
        } elseif (Yii::$app->request->get('code')) {
            // Step 2 - Receive and save access token:
            $model->scenario = ConnectShopifyStoreForm::SCENARIO_SAVE_ACCESS_TOKEN;
            $model->url = Yii::$app->request->get('shop');
            $model->code = Yii::$app->request->get('code');

            if ($model->validate()) {
                $model->saveAccessToken();

                Yii::$app->session->setFlash('success', 'Shopify shop has been connected.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('shopify', [
            'model' => $model,
            'customersList' => $this->getCustomersList(),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionReconnect(int $id): Response
    {
        $ecommerceIntegration = $this->getEcommerceIntegrationById($id);

        return match ($ecommerceIntegration->ecommercePlatform->name) {
            EcommercePlatform::SHOPIFY_PLATFORM_NAME => $this->redirect(['shopify', 'id' => $ecommerceIntegration->id]),
            default => throw new NotFoundHttpException('Ecommerce platform not found.'),
        };
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
