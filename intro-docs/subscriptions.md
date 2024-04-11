# Subscriptions - Stripe

We use **Stripe's embeddable pricing tables** for the first user payment (the first subscription). 
See [stripe.com/docs/no-code/pricing-table](https://stripe.com/docs/no-code/pricing-table). 

The pricing table product **is built for first-time purchases** and it allows you to easily drop an iframe on your webpage that shows the different price options that you offer to your new customers. 
Once you have a **[Customer](https://stripe.com/docs/api/customers/object)** that has already made a purchase, the pricing table product **should not be used**.

Instead, separately, you can let your existing customers manage their subscriptions with the **Customer Portal** [here](https://stripe.com/docs/customer-management) which has a full Stripe redirect page.
You can [pass](https://stripe.com/docs/api/customer_portal/configurations/create#create_portal_configuration-features-subscription_update-products-prices) the **list of Price ids** when creating the portal configuration so your customers can upgrade or downgrade to different prices.

Set the page `/subscription?action=subscribed` as the **page for redirect** from Stripe after a successful subscription.

## How to test webhooks:

1. Download **Stripe CLI** to your local machine. See [stripe.com/docs/stripe-cli](https://stripe.com/docs/stripe-cli).
2. Open the program and complete the **auth process**.
3. **Forward events** to a local webhook endpoint:

> stripe listen --forward-to https://shipwise.ngrok.io/subscription-webhook/stripe --skip-verify

**Copy** the `webhook signing secret` from the console and **paste** into `common\config\params-local.php`.

4. See [stripe.com/docs/webhooks/test](https://stripe.com/docs/webhooks/test) for more details and **available events/triggers**.
For instance, the command to simulate the event that occurs when a customer payment is successful:
   
> stripe trigger checkout.session.completed

5. See **types of webhook events** - [stripe.com/docs/api/events/types](https://stripe.com/docs/api/events/types).

## How to connect webhooks:

1. Go to [dashboard.stripe.com/test/developers](https://dashboard.stripe.com/test/developers) -> **Webhooks**.
2. Press the button "**Add an endpoint**".
3. For the test mode, use `https://shipwise.ngrok.io/subscription-webhook/stripe` as **Endpoint URL**.
Use the **production URL** in live mode.
Choose the **following events**: 
   
- customer.deleted
- checkout.session.completed
- customer.subscription.created
- customer.subscription.deleted
- customer.subscription.updated

4. Once the previous step is completed, **copy** `Webhook ID` and `Signing secret` and **paste** them into `common\config\params-local.php`.

## SubscriptionService:

The class `common\services\subscription\SubscriptionService.php` is used to encapsulate the main logic related to Stripe.


## Jobs:

1. Jobs are located here - `console\jobs\subscription\stripe`.
2. Run:

> php yii queue/listen --verbose


## Cron:

1. See `console\controllers\CronController.php` -> `pastDueSubscriptions()`.
We need this method to get all active subscriptions and check if they're past due.
If yes, make them inactive.

In fact, the status and update of a subscription are handled via Stripe webhooks (and our Jobs),
we need this check in case anything is wrong with Stripe webhooks processing.

> php yii cron/hourly

2. See `console\controllers\CronController.php` -> `updateSubscriptionsUsage()`.
We need this method to update subscription usage records (to sync with Stripe).
Read [stripe.com/docs/products-prices/pricing-models#reporting-usage](https://stripe.com/docs/products-prices/pricing-models#reporting-usage)
   
The method creates a new `StripeSubscriptionUpdateUsageJob` for each subscription.

> php yii cron/hourly

## Test payment details:

Card number: `4242 4242 4242 4242`  
Use a valid future date, such as `12/34`    
Use `any three-digit CVC` (four digits for American Express cards)  
Use `any value` you like for other form fields

See [stripe.com/docs/testing](https://stripe.com/docs/testing).


## Important URLs:

*API:*

- https://github.com/stripe/stripe-php
- https://stripe.com/docs/api?lang=php
- https://stripe.com/docs/api/products/object
- https://stripe.com/docs/api/checkout/sessions/object
- https://stripe.com/docs/api/subscriptions/object
- https://stripe.com/docs/api/plans/object
- https://stripe.com/docs/products-prices/pricing-models#reporting-usage
- https://stripe.com/docs/api/usage_records

*Common:*

- https://stripe.com/docs/billing/subscriptions/build-subscriptions
- https://stripe.com/docs/no-code/pricing-table
- https://stripe.com/docs/customer-management
- https://www.loom.com/share/567a4dbf009f428f930158519f2ba593
  
*Webhooks:*

- https://stripe.com/docs/webhooks
- https://stripe.com/docs/webhooks/test
- https://stripe.com/docs/api/events/types
- https://stripe.com/docs/billing/subscriptions/webhooks
- https://stripe.com/docs/webhooks/signatures
