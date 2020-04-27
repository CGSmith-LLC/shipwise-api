<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">

    <div class="jumbotron">
        <?php if (Yii::$app->user->identity->isAdmin) : ?>

            <h1>Welcome admin!</h1>
            <p class="lead">You have the power <span class="glyphicon glyphicon-sunglasses"></span></p>
            <p><?= Html::a('Manage Users', ['/user/admin'], ['class' => 'btn btn-success']) ?></p>

        <?php else : ?>

            <h1>Welcomes!</h1>
            <p class="lead">This is your ShipWise dashboard.</p>
            <p><?= Html::a('Get started', ['/order'], ['class' => 'btn btn-success']) ?></p>

        <?php endif; ?>
    </div>

    <div style="text-align: center; ">
        <?php


        echo Html::button('Open Orders ' . $orders, ['class' => 'btn btn-primary']);
        echo Html::button('Total Pending Orders' . $totalpendingorders, ['class' => 'btn btn-primary']);
        echo Html::button(Yii::t('app', 'Shipped' . $shipped), ['class' => 'btn btn-primary']);
        echo Html::button(Yii::t('app', 'Cancelled' . $cancelled), ['class' => 'btn btn-primary']);
        echo Html::button(Yii::t('app', 'WMS Errors' . $wmserrors), ['class' => 'btn btn-primary']);
        echo Html::button(Yii::t('app', 'Completed' . $completed), ['class' => 'btn btn-primary']);
        ?>
    </div>

    <div class="body-content">

        <?php /* ?>

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

        <?php */ ?>

    </div>






