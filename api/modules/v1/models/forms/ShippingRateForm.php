<?php

namespace api\modules\v1\models\forms;

use api\modules\v1\models\core\{CarrierEx, ServiceEx};
use api\modules\v1\models\forms\shipment\{Address, Package};
use api\modules\v1\models\shipping\ShipmentEx;
use common\models\ApiConsumer;
use common\models\shipping\ShipmentPackage;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *     definition = "ShippingRateForm",
 *     required   = { "sender", "recipient" },
 *
 *     @SWG\Property(
 *            property = "carrier",
 *            type = "string",
 *            enum = {"FedEx","UPS"},
 *            description = "Carrier code. You may leave it blank or unset if service field is set."
 *      ),
 *
 *     @SWG\Property(
 *            property = "service",
 *            type = "string",
 *            enum =
 *            {"FedExGround","FedEx2Day","FedEx2DayAM","FedExFirstOvernight","FedExPriorityOvernight","FedExStandardOvernight",
 *            "UPSGround","UPS3DaySelect","UPS2ndDayAir","UPS2ndDayAirAM","UPSNextDayAirSaver","UPSNextDayAir","FedExGroundHome",
 *            "FedExExpressSaver","UPSNextDayAirEarlyAM","UPSWorldwideExpressPlus","UPSWorldwideExpedited","UPSStandard",
 *            "UPSWorldwideExpress","FedEx1DayFreight","FedEx2DayFreight","FedEx3DayFreight"},
 *            description =
 *     "Specific carrier's service code. If you want to get all available services then leave this field blank or unset,
 and set the carrier field."
 *      ),
 *
 *     @SWG\Property(
 *            property = "shipmentDate",
 *            type = "string",
 *            description = "Shipment date. ISO 8601 date. If empty, date of request will be used."
 *       ),
 *
 *      @SWG\Property(
 *            property = "sender",
 *            ref = "#/definitions/ShippingAddress"
 *       ),
 *
 *      @SWG\Property(
 *            property = "recipient",
 *            ref = "#/definitions/ShippingAddress"
 *       ),
 *
 *      @SWG\Property(
 *            property = "packages",
 *            type = "array",
 *            @SWG\Items( ref = "#/definitions/Package" )
 *       ),
 *
 * )
 */

/**
 * Class ShippingRateForm
 *
 * @property string    $shipmentDate
 * @property string    $carrier
 * @property string    $service
 * @property Address   $sender
 * @property Address   $recipient
 * @property Package[] $packages
 *
 * @package api\modules\v1\models\forms
 */
class ShippingRateForm extends Model
{

    /**
     * Carrier code.
     * Can be empty if service is set.
     * @see CarrierEx::getShipwiseCodes() for list of codes
     *
     * @var string
     */
    public $carrier;

    /**
     * Service code.
     * Can be empty for all available services request, but carrier code must be set.
     * @see Service::getShipwiseCodes()
     *
     * @var string
     */
    public $service;

    /**
     * Sender object
     * @var Address
     */
    public $sender;

    /**
     * Recipient object
     * @var Address
     */
    public $recipient;

    /**
     * Packages
     * @var Package[]
     */
    public $packages = [];

    /**
     * Shipment date.
     * If empty, date of request will be used.
     * @var string
     */
    public $shipmentDate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender', 'recipient', 'packages'], 'required', 'message' => '{attribute} is required.'],
            [
                'carrier',
                'required',
                'when'    => function ($model) {
                    return (empty($this->carrier) && empty($this->service));
                },
                'message' => "Carrier code is required when service code is not set. Set the carrier to get all available services. Or set the service code to get rates for that specific service.",
            ],
            [
                'service',
                'required',
                'when'    => function ($model) {
                    return (empty($this->carrier) && empty($this->service));
                },
                'message' => "Service code is required when carrier code is not set. Set the carrier to get all available services. Or set the service code to get rates for that specific service.",
            ],
            [
                'carrier',
                'in',
                'range'   => CarrierEx::getShipwiseCodes(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', CarrierEx::getShipwiseCodes()),
            ],
            [
                'service',
                function ($attribute, $params, $validator) {
                    if (!in_array($this->$attribute, ServiceEx::getShipwiseCodes($this->carrier))) {
                        $this->addError($attribute,
                            ucfirst($attribute) . " value is incorrect. Valid values are: " .
                            implode(', ', ServiceEx::getShipwiseCodes($this->carrier))
                        );
                    }
                },
            ],
            ['packages', 'checkIsArray'],
            ['shipmentDate', 'safe'],
        ];
    }

    /**
     * Custom validator
     * Checks if attribute is an array and has at least one item
     *
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function checkIsArray($attribute, $params, $validator)
    {
        if (!(is_array($this->$attribute) && count($this->$attribute) > 0)) {
            $this->addError($attribute, '{attribute} must be an array and have at least one item.');
        }
        if (is_array($this->$attribute) && count($this->$attribute) > 0) {
            foreach ($this->$attribute as $idx => $values) {
                if (!is_numeric($idx)) {
                    $this->addError($attribute, "Incorrect schema.");
                    break;
                }
            }
        }
    }

    /**
     * Performs data validation for this model and its related models
     *
     * Errors found during the validation can be retrieved via getErrorsAll()
     *
     * @return bool whether the validation is successful without any error.
     * @see getErrorsAll()
     *
     */
    public function validateAll()
    {
        // Validate this model
        $allValidated = $this->validate();

        // sender: initialize and validate Address object
        if (isset($this->sender)) {
            $values       = (array)$this->sender;
            $this->sender = new Address();
            $this->sender->setAttributes($values);
            $allValidated = $allValidated && $this->sender->validate();
        }

        // recipient: initialize and validate Address object
        if (isset($this->recipient)) {
            $values          = (array)$this->recipient;
            $this->recipient = new Address();
            $this->recipient->setAttributes($values);
            $allValidated = $allValidated && $this->recipient->validate();
        }

        // Initialize and validate Package objects
        if (isset($this->packages) && is_array($this->packages)) {
            $params         = $this->packages;
            $this->packages = [];
            foreach ($params as $idx => $values) {
                $this->packages[$idx] = new Package();
                $this->packages[$idx]->setAttributes((array)$values);
                $allValidated = $allValidated && $this->packages[$idx]->validateAll();
            }
        }

        return $allValidated;
    }

    /**
     * Returns the errors for all attributes of this model and its related models
     *
     * @return array
     */
    public function getErrorsAll()
    {
        $errors = $this->getErrors();

        // sender
        if (is_object($this->sender) && $this->sender->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['sender' => $this->sender->getErrors()]);
        }

        // recipient
        if (is_object($this->recipient) && $this->recipient->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['recipient' => $this->recipient->getErrors()]);
        }

        // packages
        if (is_array($this->packages) && count($this->packages) > 0) {
            $pkgErrors = [];
            foreach ($this->packages as $idx => $pkg) {
                if ($pkg->hasErrors()
                    || (is_a($pkg->weight, '\yii\base\Model') && $pkg->weight->hasErrors())
                    || (is_a($pkg->dimensions, '\yii\base\Model') && $pkg->dimensions->hasErrors())
                ) {
                    $pkgErrors["package_$idx"] = $pkg->getErrorsAll();
                }
            }
            if (!empty($pkgErrors)) {
                $errors = ArrayHelper::merge($errors, ['packages' => $pkgErrors]);
            }
        }

        return $errors;
    }

    /**
     * Builds Shipment object from ShippingRateForm.
     *
     * @param ApiConsumer $apiConsumer Current authenticated API consumer
     *
     * @return ShipmentEx
     */
    public function buildShipment(ApiConsumer $apiConsumer)
    {
        /**
         * Build Shipment object from ShippingRateForm.
         */
        $shipment                = new ShipmentEx();
        $shipment->shipment_date = $this->shipmentDate ?? date("c");
        $shipment->customer_id   = $apiConsumer->customer->id ?? null;

        // Sender
        $shipment->sender_country        = $this->sender->country;
        $shipment->sender_city           = $this->sender->city;
        $shipment->sender_state          = $shipment->recognizeState($this->sender->country, $this->sender->state);
        $shipment->sender_postal_code    = $this->sender->zip;
        $shipment->sender_is_residential = ($this->sender->type == ShipmentEx::ADDRESS_TYPE_RESIDENTIAL);

        // Recipient
        $shipment->recipient_country        = $this->recipient->country;
        $shipment->recipient_city           = $this->recipient->city;
        $shipment->recipient_state          = $shipment->recognizeState($this->recipient->country,
            $this->recipient->state);
        $shipment->recipient_postal_code    = $this->recipient->zip;
        $shipment->recipient_is_residential = ($this->recipient->type == ShipmentEx::ADDRESS_TYPE_RESIDENTIAL);

        // Packaging
        $shipment->package_type = $this->packages[0]->type ?? null;
        $shipment->weight_units = $this->packages[0]->weight->units ?? null;
        $shipment->dim_units    = $this->packages[0]->dimensions->units ?? null;

        foreach ($this->packages as $package) {
            $_pkg           = new ShipmentPackage();
            $_pkg->quantity = 1;
            $_pkg->weight   = $package->weight->value;
            $_pkg->length   = $package->dimensions->length;
            $_pkg->width    = $package->dimensions->width;
            $_pkg->height   = $package->dimensions->height;
            $shipment->addPackage($_pkg);
        }

        // Carrier & service codes
        $shipment->carrier = CarrierEx::findByShipWiseCode($this->carrier);
        $shipment->service = ServiceEx::findByShipWiseCode($this->service);
        if ($shipment->service && !$shipment->carrier) {
            $shipment->carrier = CarrierEx::findByServiceCode($shipment->service->shipwise_code);
        }

        return $shipment;
    }
}