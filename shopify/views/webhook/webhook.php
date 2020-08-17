<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>

<div class="body-content">
    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-TextContainer">
            <h2 class="Polaris-Heading">Enable and Disable Webhooks</h2>
            <p>Webhooks connect your orders to ShipWise. Enable and Disable them to toggle order sync to ShipWise. This can be
                changed at any time.</p>
        </div>
    </div>
    <?= \yii\helpers\Html::a('Create Webhooks', '/webhook/create') ?>
    <?= \yii\helpers\Html::a('Delete Webhooks', '/webhook/delete') ?>

    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-SettingAction">
                    <div class="Polaris-SettingAction__Setting">Webhooks are <span
                                class="Polaris-TextStyle--variationStrong">disabled</span>.
                    </div>
                    <div class="Polaris-SettingAction__Action">
                        <button onclick="navigateToShipWise('/webhook\/create')" type="button"
                                class="Polaris-Button Polaris-Button--primary">
                            <span class="Polaris-Button__Content">
                                <span class="Polaris-Button__Text">Create</span>
                            </span>
                        </button>
                    </div>

                    <div class="Polaris-SettingAction__Action">
                        <button onclick="navigateToShipWise('/webhook\/delete')" type="button" class="Polaris-Button">
                    <span class="Polaris-Button__Content">
                        <span class="Polaris-Button__Text">Disable</span>
                    </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
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
