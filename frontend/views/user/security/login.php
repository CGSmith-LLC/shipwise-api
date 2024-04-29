<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Widget\ConnectWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\authclient\widgets\AuthChoice;

/**
 * @var yii\web\View            $this
 * @var \Da\User\Form\LoginForm $model
 * @var \Da\User\Module         $module
 */

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="#" method="POST">
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                    <div class="text-sm">
                        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    </div>
                </div>
                <div class="mt-2">
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
            Not a member?
            <a href="#" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Start a 14 day free trial</a>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnBlur' => false,
                        'validateOnType' => false,
                        'validateOnChange' => false,
                    ]
                ) ?>

                <?= $form->field(
                    $model,
                    'login',
                    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
                ) ?>

                <?= $form
                    ->field(
                        $model,
                        'password',
                        ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']]
                    )
                    ->passwordInput()
                    ->label(
                        Yii::t('usuario', 'Password')
                        . ($module->allowPasswordRecovery ?
                            ' (' . Html::a(
                                Yii::t('usuario', 'Forgot password?'),
                                ['/user/recovery/request'],
                                ['tabindex' => '5']
                            )
                            . ')' : '')
                    ) ?>

                <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>

                <?= Html::submitButton(
                    Yii::t('usuario', 'Sign in'),
                    ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']
                ) ?>

                <?php $authAuthChoice = AuthChoice::begin(['baseAuthUrl' => ['/user/security/auth']]); ?>
                <ul class="auth-clients">
                    <?php foreach($authAuthChoice->getClients() as $client): ?>
                        <li><?= $authAuthChoice->clientLink($client, '<span class="auth-icon ' . $client->getName() . '"></span> <span class="auth-text">Sign in with ' . $client->getTitle() . '</span>', ['class' => 'btn btn-block btn-social btn-' . $client->getName(),]) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php AuthChoice::end(); ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php if ($module->enableEmailConfirmation): ?>
            <p class="text-center">
                <?= Html::a(
                    Yii::t('usuario', 'Didn\'t receive confirmation message?'),
                    ['/user/registration/resend']
                ) ?>
            </p>
        <?php endif ?>
        <?php if ($module->enableRegistration): ?>
            <p class="text-center">
                <?= Html::a(Yii::t('usuario', 'Don\'t have an account? Sign up!'), ['/user/registration/register']) ?>
            </p>
        <?php endif ?>
    </div>
</div>
