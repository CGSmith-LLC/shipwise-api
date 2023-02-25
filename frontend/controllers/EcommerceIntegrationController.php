<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\filters\AccessControl;
use Da\User\Filter\AccessRuleFilter;
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
     * Connects Shopify shop.
     * @return string|Response
     * @throws ServerErrorHttpException
     */
    public function actionShopify(): string|Response
    {
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
        ]);
    }

    /**
     * Lists all EcommercePlatform models with their EcommerceIntegration for the current user.
     * @return string
     */
    public function actionIndex(): string
    {
        $ecommercePlatforms = EcommercePlatform::find()
            ->with(['ecommerceIntegration'])
            ->for(Yii::$app->user->id)
            ->orderById()
            ->all();

        return $this->render('index', [
            'models' => $ecommercePlatforms,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionConnect(): Response
    {
        $ecommercePlatform = $this->getEcommercePlatformByName(Yii::$app->request->get('platform'));

        if (!$ecommercePlatform->ecommerceIntegration) {
            $ecommerceIntegration = new EcommerceIntegration();
            $ecommerceIntegration->user_id = Yii::$app->user->id;
            /**
             * TODO: implement adding `customer_id`
             */
            //$ecommerceIntegration->customer_id = 1;
            $ecommerceIntegration->platform_id = $ecommercePlatform->id;
            $ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_CONNECTED;

            if (!$ecommerceIntegration->save()) {
                throw new ServerErrorHttpException('Ecommerce platform is not connected. Something went wrong.');
            }
        } else {
            $ecommercePlatform->ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_CONNECTED;

            if (!$ecommercePlatform->ecommerceIntegration->save()) {
                throw new ServerErrorHttpException('Ecommerce platform is not connected. Something went wrong.');
            }
        }

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been connected.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDisconnect(): Response
    {
        $ecommercePlatform = $this->getEcommercePlatformByName(Yii::$app->request->get('platform'));
        $ecommercePlatform->ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED;

        if (!$ecommercePlatform->ecommerceIntegration->save()) {
            throw new ServerErrorHttpException('Ecommerce platform is not disconnected. Something went wrong.');
        }

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been disconnected.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionPause(): Response
    {
        $ecommercePlatform = $this->getEcommercePlatformByName(Yii::$app->request->get('platform'));
        $ecommercePlatform->ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_PAUSED;

        if (!$ecommercePlatform->ecommerceIntegration->save()) {
            throw new ServerErrorHttpException('Ecommerce platform is not paused. Something went wrong.');
        }

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been paused.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionResume(): Response
    {
        $ecommercePlatform = $this->getEcommercePlatformByName(Yii::$app->request->get('platform'));
        $ecommercePlatform->ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_CONNECTED;

        if (!$ecommercePlatform->ecommerceIntegration->save()) {
            throw new ServerErrorHttpException('Ecommerce platform is not resumed. Something went wrong.');
        }

        Yii::$app->session->setFlash('success', 'Ecommerce platform has been resumed.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function getEcommercePlatformByName(string $name): EcommercePlatform
    {
        /**
         * @var EcommercePlatform $model
         */
        $model = EcommercePlatform::find()
            ->with(['ecommerceIntegration'])
            ->for(Yii::$app->user->id)
            ->where(['name' => $name])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Ecommerce platform does not exist.');
        }

        if (!$model->isActive()) {
            throw new ServerErrorHttpException('Ecommerce platform is not active.');
        }

        return $model;
    }
}
