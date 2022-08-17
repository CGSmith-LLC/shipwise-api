<?php
use Da\User\Controller\SecurityController;
use Da\User\Event\SocialNetworkAuthEvent;
use yii\base\Event;

Event::on(
    SecurityController::class, 
    SocialNetworkAuthEvent::EVENT_BEFORE_AUTHENTICATE, 
    function (SocialNetworkAuthEvent $event) {
        // $client = $event->getClient(); // $client is one of the Da\User\AuthClient\ clients
        $account = $event->getAccount(); // $account is a Da\User\Model\SocialNetworkAccount
        $account->save();
    });

// Event::on(
//     Da\User\Controller\RegistrationController::class, 
//     Da\User\Event\FormEvent::EVENT_AFTER_REGISTER, 
//     [
//         'frontend\events\user\AfterRegisterEvent',
//         'notifyAdmin'
//     ]);
