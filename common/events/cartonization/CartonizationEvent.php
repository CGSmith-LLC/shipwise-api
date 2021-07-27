<?php


use common\models\Customer;
use common\models\Item;

/**
 * Class CartonizationEvent
 *
 * @var int $customer_id
 * @var Item[] $items
 */
class CartonizationEvent extends \yii\base\Event
{
	public int $customer_id;
	public array $items;

	public function handleEvent()
	{
		switch ($this->customer_id) {
			case -1:
				$this->testCartonize();
				break;
			case self::getId('Hu Kitchens Test'):
				$this->huKitchensCartonize();
				break;
			default:
				echo 'No customer ID supplied';
				break;
		}
	}

	private static function getId(String $name): int
	{
		return Customer::findOne(['name'=>$name])->id;
	}

	private function testCartonize()
	{

	}

	private function huKitchensCartonize()
	{

	}
}