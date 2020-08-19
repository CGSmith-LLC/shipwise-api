<?php

/* @var $this yii\web\View */


$this->title = Yii::$app->name;
/* @var $webhooks \common\models\shopify\Webhook*/
?>

<div class="body-content">
    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-TextContainer">
            <h2 class="Polaris-Heading">Settings</h2>
            <p>Connect your orders to ShipWise. This
                can be changed at any time.</p>
        </div>
    </div>
    <div style="--top-bar-background:#ac1927; --top-bar-background-lighter:#3e0a27; --top-bar-color:#d91275; --p-frame-offset:0px;">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-SettingAction">
                    <?php
//                    $headers = Yii::$app->request->headers;
//
//                    $domain = $headers->get('x-shopify-shop-domain');
//                    /** @var CustomerMeta $customerMeta */
//                    $customerMeta = Yii::$app->customerSettings->getObjectByValue('shopify_store_url', $domain);
//                    $webhooks = \common\models\shopify\Webhook::find()->where(['customer_id' => $customerMeta->customer_id])->all();

                    if (!$webhooks) { ?>
                        <div class="Polaris-SettingAction__Setting">Orders are currently
                            <span
                                    class="Polaris-TextStyle--variationStrong">not connected to ShipWise.
                        </span>
                        </div>
                        <div class="Polaris-SettingAction__Action">
                            <button onclick="navigateToShipWise('/webhook\/create')" type="button"
                                    class="Polaris-Button Polaris-Button--primary">
                            <span class="Polaris-Button__Content">
                                <span class="Polaris-Button__Text">Connect
                                </span>
                            </span>
                            </button>
                        </div>
                    <?php } else { ?>
                        <div class="Polaris-SettingAction__Setting">Orders are currently
                            <span
                                    class="Polaris-TextStyle--variationStrong">connected to Shipwise.
                        </span>
                        </div>
                        <div class="Polaris-SettingAction__Action">
                            <button onclick="navigateToShipWise('/webhook\/delete')" type="button"
                                    class="Polaris-Button">
                    <span class="Polaris-Button__Content">
                        <span class="Polaris-Button__Text">Disconnect
                        </span>
                    </span>
                            </button>
                        </div>
                    <?php } ?>


                </div>
            </div>
        </div>
    </div>
</div>
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#73217f; --p-frame-offset:0px;">
    <a id="Home" class="Polaris-Button Polaris-Button--outline Polaris-Button--fullWidth" aria-label="Home"
       href="/index" data-polaris-unstyled="true">
        <span
                class="Polaris-Button__Content">
            <span
                    class="Polaris-Button__Text">Home
            </span>
        </span>
    </a>
</div>
<script>
    function navigateToShipWise(link) {
        window.location.replace(link);
    }
</script>
