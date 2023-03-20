<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use common\models\EcommercePlatform;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use Da\User\Filter\AccessRuleFilter;
use common\models\EcommerceIntegration;
use yii\web\ServerErrorHttpException;

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
