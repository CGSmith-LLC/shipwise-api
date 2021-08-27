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
use frontend\models\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use yii\console\Controller;
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
		 * @var OneTimeCharge $chargeASAP
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
			
			// Generate Invoice PDF
			$pdf = new InvoicePDF();
			$pdf->generate($invoiceToEmail);

			// Send
			\Yii::$app->queue->push(new SendEmailJob([
				'view' => 'new-invoice',
				'params' => ['model' => $invoiceToEmail],
				'to' => $invoiceToEmail->customer->getBillingEmail(),
				'bcc' => \Yii::$app->params['adminEmail'],
				'from' => \Yii::$app->params['senderEmail'],
				'subject' => 'ShipWise Invoice #' . $invoiceToEmail->id,
				'attachments' => [[
					'content' => $pdf->Output('S'),
					'options' => ['fileName' => "Invoice_{$invoiceToEmail->id}.pdf", 'contentType' => 'application/pdf'],
				]]
			]));
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

				\Yii::$app->queue->push(new SendEmailJob([
					'view' => 'failed-payment',
					'params' => [
						'customerName' => $invoice->customer->name,
						'invoiceNumber' => $invoice->id,
						'errorMessage' => $e->getMessage(),
					],
					'to' => $invoice->customer->getBillingEmail(),
					'bcc' => \Yii::$app->params['adminEmail'],
					'from' => \Yii::$app->params['senderEmail'],
					'subject' => 'Error for Invoice #' . $invoice->id,
				]));
			}
		}
		return $this;
	}
}
