<?php
    use yii\web\View;
    use common\services\subscription\SubscriptionService;

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */

    $title = 'Subscription';
    $this->params['breadcrumbs'][] = $title;
    $this->title = $title . ' - ' . Yii::$app->name;
?>

<div>
    <?php if (!$subscriptionService->hasActiveSubscription()) { ?>
        <?= $this->render('_stripe-widget', [
            'subscriptionService' => $subscriptionService,
        ]) ?>
    <?php } else { ?>
        <?= $this->render('_subscribed', [
            'subscriptionService' => $subscriptionService,
        ]) ?>
    <?php } ?>
</div>
