<?php

use frontend\assets\DatePickerAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
$this->registerJsFile('/js/dashboard-poll.js');
?>
<style>
    .customer label{
        width: 25%;
        height: 50px;
        font-size: 25px
        border-style: groove;
        border: 1px;
    }
</style>
<div id="content"></div>