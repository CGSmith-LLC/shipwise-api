<?php

namespace console\controllers;

use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\OneTimeCharge;
use common\models\Subscription;
use common\models\SubscriptionItems;
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


}