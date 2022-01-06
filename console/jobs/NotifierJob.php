<?php

namespace console\jobs;

use common\models\Customer;
use common\models\Integration;
use common\models\Order;
use frontend\models\User;
use http\Url;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\queue\RetryableJobInterface;

// @TODO the notifier job should be a bit more abstract or call on a service to find out how to send out notifications
class NotifierJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    /** @var string $message */
    public string $message;

    /** @var string $customer_reference */
    public string $customer_reference;

    /** @var string $customer_id */
    public string $customer_id;

    /** @var string $reason_general */
    public string $reason_general;

    /** @var string $reason_specific */
    public string $reason_specific;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        // Check that the order exists before we send a notification
        // If no customer reference is passed then we will send the notification without an order link
        if (empty($this->customer_reference) || $order = Order::find()
            ->where(['customer_reference' => $this->customer_reference])
            ->andWhere(['customer_id' => $this->customer_id])
            ->one()) {
            try {
                $url = null;
                // get list of emails associated with customer
                $recipients = User::find()
                    ->select(['email'])
                    ->where(['customer_id' => $this->customer_id])
                    ->andWhere(['IS', 'blocked_at', null])
                    ->all();

                // if no recipients exit because there are no users then
                if ($recipients == null) {
                    return null;
                }
                $customer = Customer::findOne($this->customer_id);

                if (isset($this->customer_reference)) {
                    $url = \yii\helpers\Url::toRoute(['order/view/'. $order->id], true);
                }

                // Send Email
                $mailer = Yii::$app->mailer;
                $mailer->viewPath = '@frontend/views/mail';
                $mailer->getView()->theme = Yii::$app->view->theme;

                $mailer->compose(['html' => 'notification'], [
                    'message' => $this->message,
                    'reason_general' => $this->reason_general,
                    'reason_specific' => $this->reason_specific,
                    'url' => $url,
                    'name' => $customer->name,
                ])
                    ->setFrom(Yii::$app->params['senderEmail'])
                    ->setTo(ArrayHelper::getColumn($recipients, 'email'))
                    ->setSubject('⚠️ Shipwise Notification!')
                    ->send();
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5); // TODO: Return to stopping attempts
    }

    public function getTtr()
    {
        return 5 * 60;
    }
}