<?php
    use yii\web\View;
    use common\services\subscription\SubscriptionService;
    use common\models\Subscription;
    use yii\helpers\{Json, Html, Url};

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */
    /* @var $meta array */

    $meta = Json::decode($subscriptionService->getActiveSubscription()->meta);
    $plan = null;
    $subscriptionStatusClass = 'text-muted';

    if ($subscriptionService->getActiveSubscription()->status == Subscription::STATUS_ACTIVE) {
        $subscriptionStatusClass = 'text-success';
    }

    if (isset($meta['plan'])) {
        $plan = $meta['plan'];
    }
?>

<h2>Your current subscription:</h2>

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <td class="col-xs-6">
                Status: <span class="<?= $subscriptionStatusClass ?>">
                    <?= ucfirst($subscriptionService->getActiveSubscription()->status) ?>
                </span>
                <br>
                Plan name:
                <span class="text-muted">
                    <?= (isset($plan['name'])) ? ucfirst(Html::encode($plan['name'])) : '&mdash;' ?>
                </span>
                <?php if (isset($plan['tiers'][0]) && isset($plan['tiers'][1])) { ?>
                    <br>
                    Details:
                    <span class="text-muted">
                        Starts at <?= Yii::$app->formatter->asCurrency(0) ?> per unit +
                        <?= Yii::$app->formatter->asCurrency(round($plan['tiers'][0]['flat_amount'] / 100, 2)) ?>/<?= Html::encode($plan['interval']) ?>
                    </span>
                <?php } ?>
                <br>
                Billing scheme:
                <span class="text-muted">
                    <?= (isset($plan['billing_scheme'])) ? ucfirst(Html::encode($plan['billing_scheme'])) : '&mdash;' ?>
                </span>
            </td>
            <td class="col-xs-6">
                Current period:
                <span class="text-muted">
                    <?= Yii::$app->formatter->asDate($subscriptionService->getActiveSubscription()->plan_period_start) ?>
                    - <?= Yii::$app->formatter->asDate($subscriptionService->getActiveSubscription()->plan_period_end) ?>
                </span>
                <?php if (isset($meta['cancel_at_period_end']) && (bool)$meta['cancel_at_period_end'] == true) { ?>
                    <br>
                    Cancel at:
                    <span class="text-muted">
                        <?= Yii::$app->formatter->asDate($meta['cancel_at']) ?>
                    </span>
                <?php } ?>
                <br>
                <i class="glyphicon glyphicon-usd"></i>
                <a href="<?= Url::to(['/subscription/invoice', 'id' => $subscriptionService->getActiveSubscription()->id]) ?>" target="_blank">Latest invoice</a>
                <br>
                <i class="glyphicon glyphicon-cog"></i>
                <a href="<?= Yii::$app->params['stripe']['customer_portal_url'] ?>" target="_blank">Manage subscription</a>
            </td>
        </tr>
    </table>
</div>
