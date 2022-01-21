<?php

namespace common\adapters\fulfillment;

use yii\base\Model;

class ThreePLCentral extends Model
{
    public int $customerIdentifier;
    public int $facilityIdentifier;
    public int $referenceNum;
    public $carrier;
    public $carrierMode;
    public $carrierAccount;
    public $billingCode;
    public $earliestShipDate;
    public $shipCancelDate;
    public $shippingNotes;
    public $notes;
    public $shipToCompany;
    public $shipToName;
    public $shipToAddress1;
    public $shipToAddress2;
    public $shipToCity;
    public $shipToState;
    public $shipToZip;
    public $shipToCountry;
    public $shipToPhone;

    public string $eTag;

    public function rules()
    {
        return [
            [['customerIdentifier'], 'required'],
            [['eTag'], 'safe'],
        ];
    }

}