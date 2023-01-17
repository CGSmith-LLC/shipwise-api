<?php


namespace unit\frontend\models;

use Codeception\Example;
use Codeception\Test\Unit as UnitTest;
use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use UnitTester;
use yii\i18n\Formatter;

class ReportFormTest extends UnitTest
{
	/** @var UnitTester $tester */
	protected UnitTester $tester;

    public function _before()
    {
        \Yii::$app->set('formatter', [
            'class' => Formatter::class,
            'timeZone' => 'America/Chicago',
            'dateFormat' => 'php:m/d/Y',
            'datetimeFormat' => 'php:m/d/Y g:ia T',
            'timeFormat' => 'php:g:i:sa e',
        ]);
    }

    public function testDate()
    {
        $model = new ReportForm(['scenario' => ReportForm::SCENARIO_BY_DATE]);

        // mock customer list
        $reflClass = new \ReflectionClass($model);
        $prop = $reflClass->getProperty('_customerList');
        $prop->setAccessible(true);
        $prop->setValue($model, [1 => new Customer()]);

        $model->start_date = '01/17/2023';
        $model->end_date = '01/19/2023';
        $model->items = '1';
        $model->customer = 1;
        $this->tester->assertTrue($model->validate(), print_r($model->getErrors(), true));
        $this->tester->assertEquals('2023-01-17 00:00:00', $model->start_date);
        $this->tester->assertEquals('2023-01-19 23:59:59', $model->end_date);

        $model->start_date = '01/17/2023';
        $model->end_date = '01/19/2022'; // end_date before start_date
        $model->items = '1';
        $model->customer = 1;
        $this->tester->assertFalse($model->validate(), print_r($model->getErrors(), true));
        $this->tester->assertEquals('2023-01-17 00:00:00', $model->start_date);
        $this->tester->assertEquals('2022-01-19 23:59:59', $model->end_date);
    }

    public function _orderNrProvider()
    {
        return [
            ['  ,', []],
            ['ABC, 123; DEF', ['ABC', '123', 'DEF']],
            [<<<CSV
ABC,
123
DEF


CSV
                , ['ABC', '123', 'DEF']],
        ];
    }

    /**
     * @dataProvider _orderNrProvider
     */
    public function testOrderNr($order_nrs, $expected)
    {
        $model = new ReportForm(['scenario' => ReportForm::SCENARIO_BY_DATE]);
        $model->order_nrs = $order_nrs;

        $this->assertEquals($expected, $model->getOrderNrs());
    }
}