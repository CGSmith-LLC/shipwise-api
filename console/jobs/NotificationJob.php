<?php

namespace console\jobs;

use frontend\models\User;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\queue\RetryableJobInterface;

class NotificationJob extends BaseObject implements RetryableJobInterface
{
    /**
     * @var int $customer_id
     */
    public int $customer_id;
    public ?int $user_id = null;

    public string $message;
    public string $subject;
    public array $url;
    public string $urlText;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        // Send Email
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = '@frontend/views/mail';
        $mailer->getView()->theme = \Yii::$app->view->theme;

        if (isset($this->user_id)) {
            $emails = User::find()
                ->where(['id' => $this->user_id])
                ->andWhere(['blocked_at' => null])
                ->all();
        } else {
            $emails = User::find()
                ->where(['customer_id' => $this->customer_id])
                ->andWhere(['blocked_at' => null])
                ->all();
        }

        $emails = ArrayHelper::map($emails, 'username', 'email');

        $mailer->compose(['html' => 'notification'], [
            'title' => $this->subject,
            'message' => $this->message,
            'url' => Url::to($this->url, true),
            'urlText' => $this->urlText,
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($emails)
            ->setSubject($this->subject)
            ->send();
    }

    public function getTtr()
    {
        return 300; // seconds
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 3);
    }
}