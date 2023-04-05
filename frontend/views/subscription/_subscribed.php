<?php
    use yii\web\View;
    use yii\helpers\Url;
    use common\services\subscription\SubscriptionService;

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */
?>

<h2>Your current subscription:</h2>

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <td class="col-xs-6">
                Status: <span class="text-muted"><?= ucfirst($subscriptionService->getActiveSubscription()->status) ?></span>
                <br>
                Paid: <span class="text-muted"><?= $subscriptionService->getActiveSubscription()->paid_amount ?>
                    <?= mb_strtoupper($subscriptionService->getActiveSubscription()->paid_currency) ?></span>
                <br>
                Plan name: <span class="text-muted"><?= ucfirst($subscriptionService->getActiveSubscription()->plan_name) ?></span>
            </td>
            <td class="col-xs-6">
                Interval: <span class="text-muted"><?= ucfirst($subscriptionService->getActiveSubscription()->plan_interval) ?></span>
                <br>
                Period: <span class="text-muted"><?= Yii::$app->formatter->asDate($subscriptionService->getActiveSubscription()->plan_period_start) ?>
                    - <?= Yii::$app->formatter->asDate($subscriptionService->getActiveSubscription()->plan_period_end) ?></span>
                <br>
                <i class="glyphicon glyphicon-usd"></i>
                <a href="<?= Url::to(['/subscription/invoice', 'id' => $subscriptionService->getActiveSubscription()->id]) ?>" target="_blank">Latest invoice</a>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center">
                <i class="glyphicon glyphicon-cog"></i>
                <a href="<?= Yii::$app->params['stripe']['customer_portal_url'] ?>" target="_blank">Manage subscription</a>
            </td>
        </tr>
    </table>
</div>
