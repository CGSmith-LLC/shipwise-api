<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace frontend\controllers\user;

use Da\User\Event\SocialNetworkConnectEvent;
use Da\User\Factory\MailFactory;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Service\UserCreateService;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\web\NotFoundHttpException;
use frontend\models\Customer;

class RegistrationController extends \Da\User\Controller\RegistrationController
{
    /**
     * {@inheritdoc}
     */
    public function actionConnect($code)
    {
        /** @var SocialNetworkAccount $account */
        $account = $this->socialNetworkAccountQuery->whereCode($code)->one();
        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->make(
            User::class,
            [],
            ['scenario' => 'connect', 'username' => $account->username, 'email' => $account->email]
        );
        $customer = new Customer([
          'direct' => 1,
          'name' => $account->username
        ]);
        // $customer->off(Customer::EVENT_BEFORE_INSERT, [$customer, 'stripeCreate']);
        $customer->save();
        $user->customer_id = $customer->id;
        $event = $this->make(SocialNetworkConnectEvent::class, [$user, $account]);

        $this->make(AjaxRequestModelValidator::class, [$user])->validate();

        if ($user->load(Yii::$app->request->post()) && $user->validate()) {
            $this->trigger(SocialNetworkConnectEvent::EVENT_BEFORE_CONNECT, $event);

            $mailService = MailFactory::makeWelcomeMailerService($user);
            if ($this->make(UserCreateService::class, [$user, $mailService])->run()) {
                $account->connect($user);
                $this->trigger(SocialNetworkConnectEvent::EVENT_AFTER_CONNECT, $event);

                Yii::$app->user->login($user, $this->module->rememberLoginLifespan);

                return $this->goBack();
            }
        }

        return $this->render(
            'connect',
            [
                'model' => $user,
                'account' => $account,
            ]
        );
    }
}
