
<script async src="https://js.stripe.com/v3/pricing-table.js"></script>
<stripe-pricing-table pricing-table-id="<?= Yii::$app->params['stripe']['pricing_table_id'] ?>"
                      publishable-key="<?= Yii::$app->params['stripe']['publishable_key'] ?>">
</stripe-pricing-table>
