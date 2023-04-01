<?php
    use yii\web\View;
    use common\services\subscription\SubscriptionService;

    /* @var $this View */
    /* @var $subscriptionService SubscriptionService */
?>

<script async src="https://js.stripe.com/v3/pricing-table.js"></script>

<stripe-pricing-table
        pricing-table-id="<?= Yii::$app->params['stripe']['pricing_table_id'] ?>"
        publishable-key="<?= Yii::$app->params['stripe']['publishable_key'] ?>"
        client-reference-id="<?= $subscriptionService->getCustomer()->id ?>"
        customer-email="<?= $subscriptionService->getCustomer()->email ?>"
</stripe-pricing-table>
