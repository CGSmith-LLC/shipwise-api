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
    We could not generate your last report for <b><?= Html::encode($customer->name) ?></b>.
    Please retry and select a smaller date range.
</p>
