<?php

namespace common\models\shipping;


use common\models\{Money, Charge};
use yii\base\Model;

/**
 * Class ShipmentRate
 *
 * @property string   $serviceCode
 * @property string   $serviceName
 * @property string   $serviceLogo
 * @property Money    $totalPrice
 * @property string   $deliveryTimeStamp
 * @property string   $deliveryDayOfWeek
 * @property string   $transitTime
 * @property string   $deliveryByTime
 * @property Charge[] $detailedCharges
 * @property string   $infoMessage
 *
 * @package common\models\shipping
 */
class ShipmentRate extends Model
{

    /** @var string */
    public $serviceCode = null;

    /** @var string */
    public $serviceName = null;

    /** @var string */
    public $serviceLogo = null;

    /** @var Money */
    public $totalPrice = null;

    /** @var string */
    public $deliveryTimeStamp = null;

    /** @var string */
    public $deliveryDayOfWeek = null;

    /** @var string */
    public $transitTime = null;
    /** @var string */
    public $deliveryByTime = null;

    /** @var Charge[] */
    public $detailedCharges = [];

    /** @var string */
    public $infoMessage = null;

    /**
     * @param Charge $item
     */
    public function addCharge(Charge $item)
    {
        $this->detailedCharges[] = $item;
    }
}