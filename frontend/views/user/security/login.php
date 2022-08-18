<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
<style type="text/css">
    .btn-social {
        margin-top: 5px;
    }

    .auth-icon {
        margin-left: 5px;
    }

    .auth-text {
        margin-top: -26px;
    }
</style>
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
                <?php foreach ($authAuthChoice->getClients() as $client) { ?>
                    <?= $authAuthChoice->clientLink($client, '<span class="auth-icon ' . $client->getName() . '"></span> <p class="auth-text">Sign in with ' . $client->getTitle() . '</p>', ['class' => 'btn-default btn btn-block btn-social btn-' . $client->getName(),]) ?>
                <?php } ?>
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
