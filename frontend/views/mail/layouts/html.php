<?php

use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>"/>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="https://fonts.googleapis.com/css?family=Montserrat|Raleway&display=swap" rel="stylesheet">
    <body>

    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>

    <div style="margin: auto; width: 73%; min-width: 420px;  max-width: 740px; padding: 10px 20px; font-family: sans-serif;">
        <p style="line-height: 1.4em; font-size: 13px; color: #a3a3a3; font-family: sans-serif;">ShipWise
            <br/>https://getshipwise.com - sales@cgsmith.net - (262) 342-6638 </p>
    </div>

    </body>
    </html>
<?php $this->endPage() ?>