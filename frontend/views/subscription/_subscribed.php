<?php
    use yii\web\View;
    use common\services\subscription\SubscriptionService;

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */
?>

<h2>Your current subscription:</h2>

<a href="<?= Yii::$app->params['stripe']['customer_portal_url'] ?>" target="_blank">Manage subscription</a>
