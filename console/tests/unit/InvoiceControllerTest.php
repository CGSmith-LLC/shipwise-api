<?php


namespace console\tests\unit;


use Codeception\Test\Unit;
use common\models\Invoice;
use console\tests\UnitTester;
use yii\base\BaseObject;

class InvoiceControllerTest extends Unit
{
	/* @var UnitTester $tester */
	protected $tester;
	
	public function testSuccessfulCharge()
	{
		$invoice = new Invoice([
			'customer_id' => 4,
			'subscription_id' => 0,
			'customer_name' => 'Jive Turkey',
			'amount' => 100,
			'balance' => 100,
			'due_date' => date('Y-m-d', time()+(2*24*60*60)),
			'status' => 0,
		]);
		
		$invoice->chargeInvoice();
		
		$this->tester->assertEquals(Invoice::STATUS_PAID, $invoice->status);
		$this->tester->assertEquals(0, $invoice->balance);
		$this->tester->assertEquals(0, $invoice->balance);
	}
	
	public function testLateCharge()
	{
		$invoice = new Invoice();
		$invoice->customer_id = 4;
		$invoice->subscription_id = 0;
		$invoice->customer_name = 'Jive Turkey';
		$invoice->amount = 100;
		$invoice->balance = 100;
		$invoice->due_date = date('Y-m-d', 1629468951);
		$invoice->status = 0;
		
		$invoice->chargeInvoice();
		
		$this->tester->assertEquals(Invoice::STATUS_LATE, $invoice->status);
		$this->tester->assertEquals(0, $invoice->balance);
		$this->tester->assertEquals(0, $invoice->balance);
	}
	
}