<?php
    use yii\web\View;
    use yii\helpers\Html;
    use common\services\subscription\SubscriptionService;

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */
?>

<script async src="https://js.stripe.com/v3/pricing-table.js"></script>

<stripe-pricing-table
        pricing-table-id="<?= Html::encode(Yii::$app->params['stripe']['pricing_table_id']) ?>"
        publishable-key="<?= Html::encode(Yii::$app->params['stripe']['publishable_key']) ?>"
        client-reference-id="<?= (int)$subscriptionService->getCustomer()->id ?>"
        customer-email="<?= Html::encode($subscriptionService->getCustomer()->email) ?>"
</stripe-pricing-table>
