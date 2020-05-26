<?php

namespace console\controllers;

use common\models\Charge;
use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\OneTimeCharge;
use common\models\Subscription;
use common\models\SubscriptionItems;
use common\models\PaymentMethod;
use frontend\models\Charges;
use frontend\models\Customer;
use frontend\models\Invoices;
use frontend\models\Payouts;
use Stripe\PaymentIntent;
use yii\console\Controller;

class InvoiceController extends Controller
{
    public $date;
    public $chargeArray = [];

    /**
     * Creating invoice from Subscriptions
     *
     * @param string $date
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
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
                $onetimecharge->added_to_invoice = 1;
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
        $invoices = Invoice::find()
            ->where(['<=', 'status', $date->format('Y-m-d')])
            ->andwhere(['<=', 'status', 1])
            ->andWhere(['>', 'balance', 0])
        ->all();

        $this->chargeInvoices($invoices);

        foreach ($this->chargeArray as $invoice_id => $charge) {
            $paymentIntent = new \frontend\models\PaymentIntent([
                'invoice_id' => $invoice_id,
                'stripe_payment_intent_id' => $charge->id,
                'amount' => $charge->amount,
                'status' => $charge->status,
                'customer_id' => $charge['customer_id'],
                'payment_method_id' => $charge['payment_method_id'],
            ]);
            $paymentIntent->save();

            if ($charge->status == 'succeeded' || $charge->status == 'processing') {
                $this->stdout("Charge successful for invoice #" . $invoice_id . PHP_EOL);
            } else {
                $this->stdout("Charge is NOT successful for invoice #" . $invoice_id . " - " . $charge->status . PHP_EOL);
            }
        }
        /**
         * Check if the charge was successful
         */
        if (isset($chargeArray->invoice_id) && (!isset($chargeArray->failure_code))) {
            if ($chargeArray->status != Invoice::STATUS_PAID) {
                $invoice = Invoice::findOne($invoice->due_date->$invoices);
                $invoice->balance = $invoice->$amount(($invoice->balance) - $chargeArray->amount);
                $invoice->update();
                $this->stdout(" charged $" . $invoice->balance . " from " . $paymentMethod . " (" . $paymentMethod->stripe_payment_method_id . ") ");
                $invoice->setAttribute('stripe_payment_method_id', $chargeArray->id);
                $invoice->update();
                $paymentMethod = PaymentMethod::find()->where(['invoice_line_item_id' => $invoice->id])->all();
                /** @var PaymentMethod $ */
                foreach ($paymentMethod as $paymentMethods) {
                    $paymentMethods->setAttribute('transfer_group', 'I' . $invoice->id);
                    $paymentMethods->save();
                }
            }

        }
    }
    /**
     * Charge invoices that are available
     * @param $invoices array of Invoice object
     * @return InvoiceController
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function chargeInvoices($invoices)
    {
        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            $customer = Customer::findOne($invoice->customer_id);
            /** @var PaymentMethod $paymentMethod */
            $paymentMethod = PaymentMethod::find()->where(['customer_id' => $customer->id, 'default' => PaymentMethod::PRIMARY_PAYMENT_METHOD_YES])->one();

            if ($paymentMethod) {
                $this->chargeArray[$invoice->id] = PaymentIntent::create([
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
                $this->chargeArray[$invoice->id]['customer_id'] = $customer->id;
                $this->chargeArray[$invoice->id]['payment_method_id'] = $paymentMethod->id;
            } else {
                $this->stderr('Payment method does not exist for invoice #' . $invoice->id . PHP_EOL);
            }
        }
        return $this;
    }
}