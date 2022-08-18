<?php

namespace frontend\events\user;

use frontend\models\User;
use Da\User\Event\FormEvent;
use Da\User\Form\RegistrationForm;
use Yii;
use yii\helpers\Url;

class AfterRegisterEvent
{

    /**
     * Send notification email to admin with link to admin page
     * Update user profile for timezone and gravatar
     *
     * @param FormEvent $event
     */
    public static function notifyAdmin(FormEvent $event)
    {
        // Find the newly registered user
        $form = $event->getForm() ?? null;
        if ($form instanceof RegistrationForm && $form->email &&
            ($user = User::findOne(['email' => $form->email])) !== null
        ) {

            // Set user profile
            $profile = $user->profile;
            $profile->timezone = Yii::$app->params['defaultTimezone'];
            $profile->gravatar_email = $user->email;
            //$user->setProfile($profile);
            $profile->save();

            // Send emails
            $params = [
                'adminUrl' => Url::to(['/user/admin/associate-customers', 'id' => $user->id], true),
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
