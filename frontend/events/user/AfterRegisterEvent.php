<?php

namespace frontend\events\user;

use dektrium\user\models\User;
use dektrium\user\events\FormEvent;
use dektrium\user\models\RegistrationForm;
use Yii;
use yii\helpers\Url;

class AfterRegisterEvent
{

    /**
     * Send notification email to admin with link to admin page
     *
     * @param FormEvent $event
     */
    public static function notifyAdmin(FormEvent $event)
    {
        // Find the newly registered user
        $form = $event->getForm() ?? null;
        if ($form instanceof RegistrationForm && $form->username &&
            ($user = User::findOne(['username' => $form->username])) !== null
        ) {
            $params = [
                'adminUrl' => Url::to(['/user/admin/update', 'id' => $user->id], true),
                'username' => $user->username,
            ];

            try {

                $mailer = Yii::$app->mailer;
                $mailer->viewPath = '@frontend/views/user/mail';
                $mailer->getView()->theme = Yii::$app->view->theme;

                $mailer->compose(['html' => 'notify-admin', 'text' => 'text/notify-admin'], $params)
                    ->setTo(Yii::$app->params['adminEmail'])
                    ->setFrom(Yii::$app->params['senderEmail'])
                    ->setSubject('New user registered')
                    ->send();

            } catch (\Exception $ex) {
                Yii::warning('Failed to send admin email notification on new user register.');
            }

        }
    }

}
