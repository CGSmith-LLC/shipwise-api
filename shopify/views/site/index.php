<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>

<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
    <p class="Polaris-DisplayText Polaris-DisplayText--sizeExtraLarge">Welcome To ShipWise.</p>
</div>
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
    <div class="Polaris-TextContainer">
        <h2 class="Polaris-Heading"></h2>
        <p>ShipWise is a system that will transmit your orders from Shopify to third party fulfillment centers, and
            return the tracking info back to you. The Shipwise app will allow you to create, update and delete orders
            through Shopify. </p>
    </div>
</div>
<?php if (empty($webhooks)) { ?>
    <div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <div class="Polaris-Banner Polaris-Banner--statusWarning Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner10Heading" aria-describedby="Banner10Content">
            <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorYellowDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
          <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
          <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path>
        </svg></span></div>
            <div class="Polaris-Banner__ContentWrapper">
                <div class="Polaris-Banner__Heading" id="Banner10Heading">
                    <p class="Polaris-Heading">You are not connected to ShipWise.</p>
                </div>
                <div class="Polaris-Banner__Content" id="Banner10Content">
                    <ul class="Polaris-List">
                        <li class="Polaris-List__Item">Your orders will not be sent. Visit your settings to connect.</li>
                    </ul>
                    <div class="Polaris-Banner__Actions">
                        <div class="Polaris-ButtonGroup">
                            <div class="Polaris-ButtonGroup__Item">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <span class="Polaris-EmptyState__Image Polaris-EmptyState__Image">
            <img
                    src="https://getshipwise.com/wp-content/uploads/2019/03/new-logo.png" alt="ShipWise Logo"
                    class="Polaris-Thumbnail__Image"
            >
        </span>
</div>
