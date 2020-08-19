<?php

/* @var $this yii\web\View */
/* @var $webhooks \common\models\shopify\Webhook*/
/* @var $created bool */
/* @var $deleted bool */

$this->title = Yii::$app->name;
?>
<?php if ($created) { ?>
    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-Banner Polaris-Banner--statusSuccess Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="Banner4Heading" aria-describedby="Banner4Content">
            <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorGreenDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
          <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
          <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m2.293-10.707L9 10.586 7.707 9.293a1 1 0 1 0-1.414 1.414l2 2a.997.997 0 0 0 1.414 0l4-4a1 1 0 1 0-1.414-1.414"></path>
        </svg></span></div>
            <div class="Polaris-Banner__ContentWrapper">
                <div class="Polaris-Banner__Heading" id="Banner4Heading">
                    <p class="Polaris-Heading">Connection Successfully Made.</p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($deleted) { ?>
    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-Banner Polaris-Banner--statusSuccess Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="Banner4Heading" aria-describedby="Banner4Content">
            <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorGreenDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
          <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
          <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m2.293-10.707L9 10.586 7.707 9.293a1 1 0 1 0-1.414 1.414l2 2a.997.997 0 0 0 1.414 0l4-4a1 1 0 1 0-1.414-1.414"></path>
        </svg></span></div>
            <div class="Polaris-Banner__ContentWrapper">
                <div class="Polaris-Banner__Heading" id="Banner4Heading">
                    <p class="Polaris-Heading">Connection Successfully Removed.</p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
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
<script>
    function navigateToShipWise(link) {
        window.location.replace(link);
    }
</script>
