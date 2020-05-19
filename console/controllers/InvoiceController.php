<?php

namespace console\controllers;

use common\models\Charge;
use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\OneTimeCharge;
use common\models\Subscription;
use common\models\SubscriptionItems;
use frontend\models\Charges;
use frontend\models\Customer;
use frontend\models\Invoices;
use common\models\PaymentMethod;
use frontend\models\Payouts;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use yii\console\Controller;

class InvoiceController extends Controller
{

    public $date;

    public function actionIndex($date = 'now')
    {
        $date = new \DateTime($date);
        //need to look for all subscriptions due today

        $subscriptions = Subscription::find()->where(['<=', 'next_invoice', $date->format('Y-m-d')])->all();


        //need to create invoices and associate them the customers
        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $totalAmount = 0; // init default value
            $items = $subscription->items;
            $invoice = new Invoice([
                'customer_id' => $subscription->customer_id,
                'subscription_id' => $subscription->id,
                'customer_name' => $subscription->customer->name,
                'due_date' => date('Y-m-d'),
                'status' => Invoice::STATUS_UNPAID,
                'amount' => 0,
                'balance' => 0,
            ]);
            $invoice->save();


            /** @var SubscriptionItems $item */
            foreach ($items as $item) {
                $invoiceItem = new InvoiceItems([
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'invoice_id' => $invoice->id,
                ]);
                $invoiceItem->save();
                $totalAmount = $totalAmount + $invoiceItem->amount;
            }


            // include one time charges
            foreach (OneTimeCharge::find()
                         ->where([
                             'customer_id' => $subscription->customer_id,
                             'added_to_invoice' => false
                         ])
                         ->all() as $onetimecharge) {
                $invoiceItem = new InvoiceItems([
                    'name' => $onetimecharge->name,
                    'amount' => $onetimecharge->amount,
                    'invoice_id' => $invoice->id,
                ]);
                $invoiceItem->save();
                $totalAmount = $totalAmount + $onetimecharge->amount;
                $onetimecharge->added_to_invoice = true;
                $onetimecharge->update();
            }

            $invoice->setAttribute('amount', $totalAmount);
            $invoice->setAttribute('balance', $totalAmount);
            $invoice->update();

            //need to update the subscription for the next invoice date
            $currentDate = new \DateTime($subscription->next_invoice);
            $period = new \DateInterval('P' . $subscription->months_to_recur . 'M');
            $subscription->next_invoice = $currentDate->add($period)->format('Y-m-d');
            $subscription->update();

            // send copy of invoice to customer's email address

        }


    }

    /**
     * @var Charge
     */

    public function actionCharge($date = 'now')
    {

        $date = new \DateTime($date);
        /** Get Invoices*/
        $invoices = Invoice::find()
            ->where(['<=', 'status', $date->format('Y-m-d')])
            ->andWhere(['>', 'balance', 0])
        ->all();

        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            $customer = Customer::findOne($invoice->customer_id);
            $paymentMethod = PaymentMethod::find()->where(['customer_id' => $customer->id, 'default' => PaymentMethod::PRIMARY_PAYMENT_METHOD_YES])->one();
            $charge = PaymentIntent::create([
                'amount' => $invoice->balance,
                'currency' => 'usd',
                'customer' => $customer->stripe_customer_id,
                'description' => 'Invoice #' . $invoice->id,
                'payment_method' => $paymentMethod->stripe_payment_method_id,
                'confirm' => true,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_url' => 'https://app.getshipwise.com/invoice/view?id=' . $invoice->id,
                ],
            ]);
        }
    }
}