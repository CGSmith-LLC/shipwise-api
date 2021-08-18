<?php

namespace console\controllers;

use common\models\Charge;
use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\OneTimeCharge;
use common\models\Subscription;
use common\models\SubscriptionItems;
use common\models\PaymentMethod;
use common\pdf\InvoicePDF;
use common\pdf\ReceiptPDF;
use console\jobs\SendEmailJob;
use dektrium\user\models\User;
use frontend\models\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use yii\base\BaseObject;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
        $invoicesToEmail = [];

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
            if ($invoice->update() == false) {
                foreach ($invoice->getErrorSummary(true) as $error) {
                    echo 'actionIndex() Invoice #' . $invoice->id . ' ' . $error . PHP_EOL;
                }
            } else {
                $invoicesToEmail[] = $invoice->id;
            }

            //need to update the subscription for the next invoice date
            $currentDate = new \DateTime($subscription->next_invoice);
            $period = new \DateInterval('P' . $subscription->months_to_recur . 'M');
            $subscription->next_invoice = $currentDate->add($period)->format('Y-m-d');
            $subscription->update();
        }

        /**
         * Check any onetimes that need to be sent out
         */
        $lastASAPCustomer = 0;
        foreach (OneTimeCharge::find()
                     ->where(['added_to_invoice' => false])
                     ->andWhere(['charge_asap' => true])
                     ->orderBy(['customer_id' => SORT_ASC])
                     ->all() as $chargeASAP) {

            // Create invoice if this is a new customer ID
            if ($lastASAPCustomer != $chargeASAP->customer_id) {
                $totalAmount = 0; // init default value
                $invoice = new Invoice([
                    'customer_id' => $chargeASAP->customer_id,
                    'subscription_id' => 0, // One time charge ASAP - no subscription
                    'customer_name' => $chargeASAP->customer->name,
                    'due_date' => date('Y-m-d'),
                    'status' => Invoice::STATUS_UNPAID,
                    'amount' => 0,
                    'balance' => 0,
                ]);
                $invoice->save();
                $invoicesToEmail[] = $invoice->id;
            }

            // create invoice line item
            $invoiceItem = new InvoiceItems([
                'name' => $chargeASAP->name,
                'amount' => $chargeASAP->amount,
                'invoice_id' => $invoice->id,
            ]);
            $invoiceItem->save();
            $totalAmount += $chargeASAP->amount;

            // Update one time as being added
            $chargeASAP->added_to_invoice = 1;
            $chargeASAP->update();

            // update invoice with total amount
            $invoice->setAttribute('amount', $totalAmount);
            $invoice->setAttribute('balance', $totalAmount);
            $invoice->update();

            // set last customer with current customer id
            $lastASAPCustomer = $chargeASAP->customer_id;
        }

        // send copy of invoice to customer's email address
        foreach ($invoicesToEmail as $invoice_id) {
            /** @var $invoiceToEmail Invoice */
            $invoiceToEmail = Invoice::findOne($invoice_id);

            $customerEmails = $invoiceToEmail->customer->getCustomerEmails();

            try {

                // Generate Invoice PDF
                $pdf = new InvoicePDF();
                $pdf->generate($invoiceToEmail);

                // Send
				\Yii::$app->queue->push(new SendEmailJob([
					'view' => 'new-invoice',
					'params' => serialize(['model' => $invoiceToEmail]),
					'to' => implode(separator: PHP_EOL, array: $customerEmails),
					'bcc' => \Yii::$app->params['adminEmail'],
					'from' => \Yii::$app->params['senderEmail'],
					'subject' => 'ShipWise Invoice #' . $invoiceToEmail->id,
					'attachments' => serialize([[
						'content' => $pdf->Output('S'),
						'options' => ['fileName' => "Invoice_{$invoiceToEmail->id}.pdf", 'contentType' => 'application/pdf'],
					]])
				]));

            } catch (\Exception $ex) {
                var_dump($ex->getMessage());
                die('exception hit');
            }
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
            /**
             * Save Stripe ID after charging the customer
             */
            $paymentIntent = new \frontend\models\PaymentIntent([
                'invoice_id' => $invoice_id,
                'stripe_payment_intent_id' => $charge->id,
                'amount' => $charge->amount,
                'status' => $charge->status,
                'customer_id' => $charge['customer_id'],
                'payment_method_id' => $charge['payment_method_id'],
            ]);
            $paymentIntent->save();

            /**
             * 1. Get invoice
             * 2. Update balance maybe?
             * 3. Update status maybe?
             * 4. update()
             */
            $invoice = Invoice::findOne($invoice_id);

            // check stripe's status
            if ($charge->status == 'succeeded') {
                // update invoice.balance to the remaining amount minus stripe's charges
                $invoice->setAttribute('balance', ($invoice->amount - $charge->amount));
                $invoice->setAttribute('status', Invoice::STATUS_PAID);
                $invoice->setAttribute('stripe_charge_id', $charge->id);
                if ($invoice->update() == false) {
                    foreach ($invoice->getErrorSummary(true) as $error) {
                        echo 'actionCharge() Invoice #' . $invoice->id . ' ' . $error . PHP_EOL;
                    }
                } else {
					$customerEmails = $invoice->customer->getCustomerEmails();

                    try {

                        // Generate Receipt PDF
                        $pdf = new ReceiptPDF();
                        $pdf->generate($invoice);

                        // Send
						\Yii::$app->queue->push(new SendEmailJob([
							'view' => 'new_payment',
							'params' => serialize([
								'model' => $invoice,
								'url' => Url::toRoute(['invoice/view', 'id' => $invoice->id], 'https'),
							]),
							'to' => implode(separator: PHP_EOL, array: $customerEmails),
							'bcc' => \Yii::$app->params['adminEmail'],
							'from' => \Yii::$app->params['senderEmail'],
							'subject' => 'ShipWise Receipt for Invoice #' . $invoice->id,
							'attachments' => serialize([
								'content' => $pdf->Output('S'),
								'options' => ['fileName' => "Receipt_{$invoice->id}.pdf", 'contentType' => 'application/pdf'],
							]),
						]));

                    } catch (\Exception $ex) {
                        var_dump($ex->getMessage());
                        die('exception hit');
                    }
                }
            }

            if ($charge->status == 'succeeded' || $charge->status == 'processing') {
                $this->stdout("Charge successful for invoice #" . $invoice_id . PHP_EOL);
            } else {
                $this->stdout("Charge is NOT successful for invoice #" . $invoice_id . " - " . $charge->status . PHP_EOL);
            }
        }
    }

    /**
     * Charge invoices that are available
     * @param $invoices array of Invoice object
     * @return InvoiceController
     */
    protected function chargeInvoices($invoices)
    {
        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            $customer = Customer::findOne($invoice->customer_id);
            /** @var PaymentMethod $paymentMethod */
            $paymentMethod = PaymentMethod::find()->where([
                'customer_id' => $customer->id,
                'default' => PaymentMethod::PRIMARY_PAYMENT_METHOD_YES
            ])->one();

			try {
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
							'invoice_url' => Url::toRoute(['invoice/view', 'id' => $invoice->id], 'https'),
						],
					]);
					$this->chargeArray[$invoice->id]['customer_id'] = $customer->id;
					$this->chargeArray[$invoice->id]['payment_method_id'] = $paymentMethod->id;
				} else {
					$this->stderr('Payment method does not exist for invoice #' . $invoice->id . PHP_EOL);
				}
			} catch (ApiErrorException $e) {
				$this->stderr(string: "There was an error processing invoice #{$invoice->id}: {$e->getMessage()}" . PHP_EOL . 'See Log for more details.' . PHP_EOL);
				\Yii::error($e->getError()->toJSON());

				$content = '<h1>Error for Invoice #' . $invoice->id . '</h1>'
					. '<p>There was an error completeing invoice #' . $invoice->id . '.</p>'
					. '<p>The error message reads: ' . $e->getMessage() . '</p>';

				$customerEmails = $invoice->customer->getCustomerEmails();

				\Yii::$app->queue->push(new SendEmailJob([
					'view' => 'layouts\html',
					'params' => serialize([
						'content' => $content,
					]),
					'to' => implode(separator: PHP_EOL, array: $customerEmails),
					'bcc' => \Yii::$app->params['adminEmail'],
					'from' => \Yii::$app->params['senderEmail'],
					'subject' => 'Error for Invoice #' . $invoice->id,
				]));
			}
        }
        return $this;
    }
}
