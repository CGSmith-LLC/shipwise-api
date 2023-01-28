<?php

use yii\bootstrap\Nav;

/**
 * This view overrides @dektrium/user/views/admin/update.php
 */

/**
 * @var \yii\web\View $this
 * @var frontend\models\User $user
 * @var string $content
 */

$this->title = Yii::t('usuario', 'Update user account') . ' - ID ' . $user->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('usuario', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<?= $this->render('/shared/_menu') ?>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills nav-stacked',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('usuario', 'Account details'),
                            'url' => ['/user/admin/update', 'id' => $user->id]
                        ],
                        [
                            'label' => Yii::t('usuario', 'Profile details'),
                            'url' => ['/user/admin/update-profile', 'id' => $user->id]
                        ],
                        [
                            'label' => Yii::t('usuario', 'Information'),
                            'url' => ['/user/admin/info', 'id' => $user->id]
                        ],
                        [
                            'label' => Yii::t('usuario', 'Assignments'),
                            'url' => ['/user/admin/assignments', 'id' => $user->id],
                            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
                        ],
                        '<hr>',
                        [
                            'label' => Yii::t('usuario', 'Associate customers'),
                            'url' => ['/user/admin/associate-customers', 'id' => $user->id]
                        ],
                        [
                            'label' => Yii::t('usuario', 'Associate warehouses'),
                            'url' => ['/user/admin/associate-warehouses', 'id' => $user->id]
                        ],
                        '<hr>',
                        [
                            'label' => Yii::t('usuario', 'Confirm'),
                            'url' => ['/user/admin/confirm', 'id' => $user->id],
                            'visible' => !$user->isConfirmed,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('usuario', 'Block'),
                            'url' => ['/user/admin/block', 'id' => $user->id],
                            'visible' => !$user->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure you want to block this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('usuario', 'Unblock'),
                            'url' => ['/user/admin/block', 'id' => $user->id],
                            'visible' => $user->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure you want to unblock this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('usuario', 'Delete'),
                            'url' => ['/user/admin/delete', 'id' => $user->id],
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
