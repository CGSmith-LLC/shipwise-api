<?php

namespace frontend\models\forms;

use console\jobs\CreateReportJob;
use frontend\models\Customer;
use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;

/**
 * Class ReportForm
 *
 * @package frontend\models\forms
 *
 * @property string $start_date
 * @property string $end_date
 * @property int    $customer
 */
class ReportForm extends Model
{
    public const SCENARIO_BY_DATE = 'date';
    public const SCENARIO_BY_ORDER_NR = 'order_nr';

    public $start_date;
    public $end_date;
    public $customer;
    public $items = true;
    public $order_nrs;


    public function rules()
    {
        return [
            [['start_date', 'end_date', 'customer'], 'default', 'value' => null],
            [['start_date', 'end_date', 'customer'], 'required', 'on' => self::SCENARIO_BY_DATE],

            ['start_date', 'date', 'on' => self::SCENARIO_BY_DATE,
                'timestampAttribute' => 'start_date',
                // Always set for beginning of day and end of day for query
                'timestampAttributeFormat' => 'php:Y-m-d 00:00:00'
            ],
            ['end_date', 'date', 'on' => self::SCENARIO_BY_DATE,
                'timestampAttribute' => 'end_date',
                // Always set for beginning of day and end of day for query
                'timestampAttributeFormat' => 'php:Y-m-d 23:59:59'
            ],

            ['end_date', 'compare', 'operator' => '>=', 'compareAttribute' => 'start_date', 'on' => self::SCENARIO_BY_DATE, 'enableClientValidation' => false],

            [
                'customer',
                'in',
                'range' => array_keys($this->getCustomerList()),
                'on' => self::SCENARIO_BY_DATE
            ],

            [['order_nrs'], 'required', 'on' => self::SCENARIO_BY_ORDER_NR],

            [['items'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'order_nrs' => 'Order Numbers',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_BY_DATE => ['start_date', 'end_date', 'customer', 'items'],
            self::SCENARIO_BY_ORDER_NR => ['order_nrs', 'items'],
        ];
    }

    public function getOrderNrs()
    {
        return preg_split('~[;,\s]+~', $this->order_nrs, -1, PREG_SPLIT_NO_EMPTY);
    }

    private $_customerList;
    /**
     * @return array
     */
    public function getCustomerList(): array
    {
        if ($this->_customerList !== null) {
            return $this->_customerList;
        }
        return $this->_customerList = Yii::$app->user->identity->isAdmin
            ? Customer::getList()
            : Yii::$app->user->identity->getCustomerList();
    }

    public function pushReportQueueJob()
    {
        switch ($this->scenario) {
            case self::SCENARIO_BY_DATE:
                $job = new CreateReportJob([
                    'customer_ids' => [$this->customer],
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'items' => $this->items,
                ]);
                break;
            case self::SCENARIO_BY_ORDER_NR:
                $job = new CreateReportJob([
                    'order_nrs' => $this->getOrderNrs(),
                    'customer_ids' => array_keys($this->getCustomerList()),
                    'items' => $this->items,
                ]);
                break;
            default:
                throw new InvalidCallException('Unknown model scenario: ' . $this->scenario);
        }

        $job->user_id = Yii::$app->user->id;
        $job->user_email = Yii::$app->user->identity->email;

        Yii::$app->queue->push($job);
    }
}
