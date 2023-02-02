<?php
use yii\helpers\Html;
use common\models\Customer;

/* @var $this yii\web\View */
/* @var $customer Customer */
?>

<p>
    Hello,
</p>
<p>
    Please regenerate your last report for <b><?= Html::encode($customer->name) ?></b>
    since we had some issues executing it. Try to select a shorter interval.
</p>
