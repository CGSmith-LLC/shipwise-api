<?php

namespace console\jobs;

use common\models\Customer;
use common\models\Order;
use frontend\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\queue\RetryableJobInterface;

// @TODO the notifier job should be a bit more abstract or call on a service to find out how to send out notifications
class AdminNotifierJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    /** @var string $message */
    public string $message;

    /** @var string $debug */
    public string $debug;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        // Send Email
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = '@frontend/views/mail';
        $mailer->getView()->theme = Yii::$app->view->theme;

        $mailer->compose(['html' => 'admin-notification'], [
            'message' => $this->message,
            'debug' => $this->debug,
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('⚠️ Shipwise Notification!')
            ->send();
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