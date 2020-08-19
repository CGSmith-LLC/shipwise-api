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
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
    <a target="_blank"
       class="Polaris-Button Polaris-Button--outline Polaris-Button--monochrome Polaris-Button--fullWidth"
       href="https://app.getshipwise.com" rel="noopener noreferrer" data-polaris-unstyled="true">
        <span
                class="Polaris-Button__Content">
            <span class="Polaris-Button__Text">View Orders
            </span>
        </span>
    </a>
</div>

<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
    <a class="Polaris-Button Polaris-Button--outline Polaris-Button--fullWidth" aria-label="View Settings"
       href="/webhook" data-polaris-unstyled="true">
        <span
                class="Polaris-Button__Content">
            <span
                    class="Polaris-Button__Text">View Settings
            </span>
        </span>
    </a>
</div>
<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
        <span class="Polaris-EmptyState__Image Polaris-EmptyState__Image">
            <img
                    src="https://getshipwise.com/wp-content/uploads/2019/03/new-logo.png" alt="ShipWise Logo"
                    class="Polaris-Thumbnail__Image"
            >
        </span>
</div>
