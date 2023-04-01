<?php

namespace frontend\controllers;

use Yii;
use yii\web\{Response, BadRequestHttpException, ServerErrorHttpException};
use yii\helpers\Json;
use common\models\SubscriptionWebhook;

/**
 * Class SubscriptionWebhookController
 * @package frontend\controllers
 */
class SubscriptionWebhookController extends Controller
{
    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function actionStripe(): array
    {
        //file_put_contents('stripe.txt', Yii::$app->request->rawBody);

        if (!Yii::$app->request->rawBody) {
            throw new ServerErrorHttpException('No request body.');
        }

        $data = Json::decode(Yii::$app->request->rawBody);

        if (!isset($data['type'])) {
            throw new ServerErrorHttpException('Event type is not valid.');
        }

        $subscriptionWebhook = $this->getNewSubscriptionWebhookObject(SubscriptionWebhook::PAYMENT_METHOD_STRIPE);
        $subscriptionWebhook->event = $data['type'];
        $subscriptionWebhook->payload = Yii::$app->request->rawBody;

        if (!$subscriptionWebhook->save()) {
            throw new ServerErrorHttpException('Event not saved.');
        }

        return [
            'status' => 200,
            'result' => 'success',
        ];
    }

    protected function getNewSubscriptionWebhookObject(string $paymentMethodName): SubscriptionWebhook
    {
        $subscriptionWebhook = new SubscriptionWebhook();
        $subscriptionWebhook->payment_method = $paymentMethodName;
        $subscriptionWebhook->status = SubscriptionWebhook::STATUS_RECEIVED;

        return $subscriptionWebhook;
    }
}
