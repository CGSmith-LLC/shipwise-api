<?php

namespace common\models;

use common\models\query\InvoiceQuery;
use common\pdf\ReceiptPDF;
use console\jobs\SendEmailJob;
use frontend\models\Customer;
use frontend\models\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use yii\base\BaseObject;
use yii\helpers\Url;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $customer_id      Reference to customer
 * @property int $subscription_id  Reference to Subscription ID
 * @property string $customer_name    Customer Name
 * @property int $amount           Total in Cents
 * @property int $balance          Balance Due in Cents
 * @property string $due_date         Due Date
 * @property string $stripe_charge_id stripe charge id
 * @property int $status           Status of transaction
 *
 * @property Customer $customer
 * @property InvoiceItems[] $items
 * @property Subscription[] $subscription
 * @property PaymentIntent $paymentIntent
 */
class Invoice extends \yii\db\ActiveRecord
{
	public const STATUS_UNPAID = 1;
	public const STATUS_PAID = 2;
	public const STATUS_LATE = 3;
	
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'invoice';
	}
	
	/**
	 * @inheritdoc
	 * @return InvoiceQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new InvoiceQuery(get_called_class());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				['customer_id', 'subscription_id', 'customer_name', 'amount', 'balance', 'due_date', 'status'],
				'required',
			],
			[['customer_id', 'subscription_id', 'amount', 'balance', 'status'], 'integer'],
			[['due_date'], 'safe'],
			[['customer_name'], 'string', 'max' => 64],
			[['stripe_charge_id'], 'string', 'max' => 128],
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'customer_id' => 'Customer ID',
			'subscription_id' => 'Subscription ID',
			'customer_name' => 'Customer Name',
			'amount' => 'Amount',
			'balance' => 'Balance',
			'due_date' => 'Due Date',
			'stripe_charge_id' => 'Stripe Charge ID',
			'status' => 'Status',
		];
	}
	
	/**
	 * Returns decimal amount after getting from database
	 *
	 * @return float
	 */
	public function getDecimalAmount()
	{
		return $this->amount / 100;
	}
	
	/**
	 * Status label
	 *
	 * @param bool $html Whether to return in html format
	 *
	 * @return string
	 */
	public function getStatusLabel($html = true)
	{
		$status = '';
		switch ($this->status)
		{
			case self::STATUS_UNPAID:
				$status = $html ? '<p class="label label-primary">Unpaid</p>' : 'Unpaid';
				break;
			case self::STATUS_PAID:
				$status = $html ? '<p class="label label-success">Paid</p>' : 'Paid';
				break;
			case self::STATUS_LATE:
				$status = $html ? '<p class="label label-danger">Past Due</p>' : 'Past Due';
				break;
		}
		
		return $status;
	}
	
	
	/**
	 * Relation for InvoiceItems
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getItems()
	{
		return $this->hasMany(InvoiceItems::class, ['invoice_id' => 'id']);
	}
	
	/**
	 * Relation for Customers
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer()
	{
		return $this->hasOne(Customer::class, ['id' => 'customer_id']);
	}
	
	/**
	 * Relation for Subscriptions
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSubscription()
	{
		return $this->hasMany(Subscription::class, ['id' => 'subscription_id']);
	}
	
	/**
	 * Relation for PaymentIntent
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getPaymentIntent()
	{
		return $this->hasOne(PaymentIntent::class, ['invoice_id' => 'id']);
	}
	
	public function chargeInvoice()
	{
		/**
		 * 1. Charge
		 * 2. Save payment intent
		 * 3. Update
		 * 4. Receipt
		 */
		
		$customer = Customer::findOne($this->customer_id);
		/** @var PaymentMethod $paymentMethod */
		$paymentMethod = PaymentMethod::find()->where([
			'customer_id' => $customer->id,
			'default' => PaymentMethod::PRIMARY_PAYMENT_METHOD_YES
		])->one();
		
		try
		{
			if ($paymentMethod)
			{
				$chargeItem = \Stripe\PaymentIntent::create([
					'amount' => $this->balance,
					'currency' => 'usd',
					'customer' => $customer->stripe_customer_id,
					'description' => 'Invoice #' . $this->id,
					'payment_method' => $paymentMethod->stripe_payment_method_id,
					'confirm' => true,
					'metadata' => [
						'invoice_id' => $this->id,
						'invoice_url' => Url::toRoute(['invoice/view', 'id' => $this->id], 'https'),
					],
				]);
				$chargeItem['customer_id'] = $customer->id;
				$chargeItem['payment_method_id'] = $paymentMethod->id;
			}
			else
			{
				$this->stderr('Payment method does not exist for invoice #' . $this->id . PHP_EOL);
			}
		} catch (ApiErrorException $e)
		{
			$this->stderr(string: "There was an error processing invoice #{$this->id}: {$e->getMessage()}" . PHP_EOL . 'See Log for more details.' . PHP_EOL);
			\Yii::error($e->getError()->toJSON());
			
			\Yii::$app->queue->push(new SendEmailJob([
				'view' => 'failed-payment',
				'params' => [
					'customerName' => $this->customer->name,
					'invoiceNumber' => $this->id,
					'errorMessage' => $e->getMessage(),
				],
				'to' => $this->customer->getBillingEmail(),
				'bcc' => \Yii::$app->params['adminEmail'],
				'from' => \Yii::$app->params['senderEmail'],
				'subject' => 'Error for Invoice #' . $this->id,
			]));
		}
		
		/**
		 * Save Stripe ID after charging the customer
		 */
		$paymentIntent = new \frontend\models\PaymentIntent([
			'invoice_id' => $this->id,
			'stripe_payment_intent_id' => $chargeItem->id,
			'amount' => $chargeItem->amount,
			'status' => $chargeItem->status,
			'customer_id' => $chargeItem['customer_id'],
			'payment_method_id' => $chargeItem['payment_method_id'],
		]);
		$paymentIntent->save();
		
		/**
		 * 1. Get invoice
		 * 2. Update balance maybe?
		 * 3. Update status maybe?
		 * 4. update()
		 */
		
		// check stripe's status
		if ($chargeItem->status == 'succeeded')
		{
			// update invoice.balance to the remaining amount minus stripe's charges
			$this->setAttribute('balance', ($this->amount - $chargeItem->amount));
			$this->setAttribute('status', Invoice::STATUS_PAID);
			$this->setAttribute('stripe_charge_id', $chargeItem->id);
			if ($this->update() == false)
			{
				foreach ($this->getErrorSummary(true) as $error)
				{
					echo 'actionCharge() Invoice #' . $this->id . ' ' . $error . PHP_EOL;
				}
			}
			else
			{
				try
				{
					
					// Generate Receipt PDF
					$pdf = new ReceiptPDF();
					$pdf->generate($this);
					
					// Send
					\Yii::$app->queue->push(new SendEmailJob([
						'view' => 'new_payment',
						'params' => [
							'model' => $this,
							'url' => Url::toRoute(['invoice/view', 'id' => $this->id], 'https'),
						],
						'to' => $this->customer->getBillingEmail(),
						'bcc' => \Yii::$app->params['adminEmail'],
						'from' => \Yii::$app->params['senderEmail'],
						'subject' => 'ShipWise Receipt for Invoice #' . $this->id,
						'attachments' => [[
							'content' => $pdf->Output('S'),
							'options' => ['fileName' => "Receipt_{$this->id}.pdf", 'contentType' => 'application/pdf'],
						]],
					]));
					
				} catch (\Exception $ex)
				{
					var_dump($ex->getMessage());
					die('exception hit');
				}
			}
		}
		
		if ($chargeItem->status == 'succeeded' || $chargeItem->status == 'processing')
		{
			$this->stdout("Charge successful for invoice #" . $this->id . PHP_EOL);
		}
		else
		{
			$this->stdout("Charge is NOT successful for invoice #" . $this->id . " - " . $chargeItem->status . PHP_EOL);
		}
		
	}
}
