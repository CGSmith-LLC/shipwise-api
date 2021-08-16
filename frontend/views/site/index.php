<?php

use frontend\assets\DatePickerAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
$this->registerJsFile('/js/dashboard-poll.js');
$this->registerCss("@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Roboto&display=swap');")
?>
<style>
    .number-box {
        padding: 2rem;
        text-align: center;
        font-weight: bold;
        font-size: 18px;
    }

    .margin-right {
        margin-right: .15rem;
    }

    .blue {
        background: rgb(191, 219, 254);
    }

    .green {
        margin-left: 5px;
        margin-right: 5px;
        background: rgb(167, 243, 208);
    }

    .red {
        background: rgb(254, 202, 202);
    }

    .cole-head {
        font-family: 'Montserrat', sans-serif;
        font-weight: bold;
        text-align: center;
    }

    .customer {
        font-family: 'Roboto', sans-serif;
        padding-top: 15px;
        font-weight: bold;
    }

    .real-customer {
        padding-top: 8px;
        padding-left: 40px;
    }

    .row-bottom {
        margin-bottom: 2px;
    }

    .avatar {
        width: 34px;
        height: 34px;
        display: flex;
        float: left;
        align-items: center;
        justify-content: center;
        background-color: #ccc;
        border-radius: 50%;
        font-family: sans-serif;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
    }
</style>
<div id="content"></div>