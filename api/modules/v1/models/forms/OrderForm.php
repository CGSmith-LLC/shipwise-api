<?php

namespace api\modules\v1\models\forms;

use api\modules\v1\models\forms\shipment\Package;
use api\modules\v1\models\order\PackageEx;
use common\models\State;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\order\StatusEx;

/**
 * @SWG\Definition(
 *     definition = "OrderForm",
 *     required   = { "customerReference", "shipTo", "items" },
 *     @SWG\Property(
 *            property = "orderReference",
 *            type = "string",
 *            description = "Order reference - order number from fulfillment side",
 *            minLength = 1,
 *            maxLength = 45
 *        ),
 *     @SWG\Property(
 *            property = "customerReference",
 *            type = "string",
 *            description = "Customer reference - order number from Ecommerce or customer side",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "notes",
 *            type = "string",
 *            description = "Notes that will display under order info for ShipWise",
 *            minLength = 1,
 *            maxLength = 6000
 *        ),
 *     @SWG\Property(
 *            property = "requestedShipDate",
 *            type = "string",
 *            description = "When the order should ship and be fulfilled",
 *        ),
 *     @SWG\Property(
 *            property = "mustArriveByDate",
 *            type = "string",
 *            description = "When the order needs to arrive at the customer",
 *        ),
 *     @SWG\Property(
 *            property = "shipTo",
 *            ref = "#/definitions/AddressForm"
 *        ),
 *     @SWG\Property(
 *            property = "tracking",
 *            ref = "#/definitions/TrackingForm"
 *        ),
 *     @SWG\Property(
 *            property = "status",
 *            type = "integer",
 *            enum = {1,2,6,7,8,9,10,11},
 *            default = "9",
 *            description = "Order status
 *                    1  - Shipped
 *                    2  - Amazon Prime
 *                    6  - On Hold
 *                    7  - Cancelled
 *                    8  - Pending Fulfillment
 *                    9  - Open
 *                    10 - WMS Error
 *                    11 - Completed",
 *       ),
 *     @SWG\Property(
 *            property = "items",
 *            type = "array",
 *            @SWG\Items( ref = "#/definitions/ItemForm" )
 *        ),
 *     @SWG\Property(
 *            property = "uuid",
 *            type = "string",
 *            description = "Unique identifier from ecommerce platform",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "poNumber",
 *            type = "string",
 *            description = "PO number from ecommerce platform. Useful for the 3PL to look up information.",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "origin",
 *            type = "string",
 *            description = "Origination platform of the order. Such as SquareSpace, WooCommerce, Zoho, etc.",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "carrier_id",
 *            type = "integer",
 *        ),
 *     @SWG\Property(
 *            property = "service_id",
 *            type = "integer"
 *        ),
 * )
 */

/**
 * Class OrderForm
 *
 * @package api\modules\v1\models\forms
 */
class OrderForm extends Model
{

    const SCENARIO_DEFAULT = 'default'; // the create scenario
    const SCENARIO_UPDATE = 'update';  // the update scenario
    const SCENARIO_DELETE = 'delete';  // the delete scenario

    /** @var string */
    public $uuid;
    /** @var string */
    public $poNumber;
    /** @var string */
    public $notes;
    /** @var string */
    public $origin;
    /** @var string */
    public $carrier_id;
    /** @var string */
    public $service_id;
    /** @var string */
    public $orderReference;
    /** @var string */
    public $customerReference;
    /** @var string */
    public $requestedShipDate;
    /** @var string */
    public $mustArriveByDate;
    /** @var AddressForm */
    public $shipTo;
    /** @var AddressForm */
    public $shipFrom;
    /** @var TrackingForm */
    public $tracking;
    /** @var PackageForm[] */
    public $packages;
    /** @var string */
    public $status;
    /** @var ItemForm[] */
    public $items;
    /** @var integer */
    public $transit;
    /** @var string  */
    public $packagingNotes;
    /** @var string */
    public $execution_log;
    /** @var string */
    public $error_log;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['customerReference', 'shipTo', 'items'],
                'required',
                'message' => '{attribute} is required.',
            ],
            [['poNumber', 'uuid', 'origin', 'customerReference', 'packagingNotes'], 'string', 'length' => [1, 64]],
            ['orderReference', 'string', 'length' => [1, 45]],
            ['notes', 'string', 'length' => [1, 6000]],
            [['execution_log', 'error_log'], 'string', 'max' => 500],
            ['transit', 'integer'],
            [['requestedShipDate', 'mustArriveByDate'], 'date', 'format' => 'php:Y-m-d'],
            ['status', 'required', 'on' => self::SCENARIO_UPDATE, 'message' => '{attribute} is required.'],
            ['status', 'integer', 'on' => self::SCENARIO_UPDATE],
            [
                'status',
                'in',
                'range' => StatusEx::getIdsAsArray(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', StatusEx::getIdsAsArray()),
            ],
            [['items', 'packages'], 'checkIsArray'],
            [['tracking', 'packages', 'service_id', 'carrier_id', 'shipFrom'], 'safe'],
        ];
    }

    public function beforeValidate()
    {
        if (isset($this->requestedShipDate)) {
            $this->requestedShipDate = new \DateTime($this->requestedShipDate);
            $this->requestedShipDate = $this->requestedShipDate->format('Y-m-d');
        }
        if (isset($this->mustArriveByDate)) {
            $this->mustArriveByDate = new \DateTime($this->mustArriveByDate);
            $this->mustArriveByDate = $this->mustArriveByDate->format('Y-m-d');
        }
        \Yii::debug($this);
        return parent::beforeValidate();
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
    }

    private function addressStateValidation($address)
    {
        $values = (array)$this->$address;
        // If stateId is not set but state exists then try to reference the state's value in the DB
        if (isset($values['state']) && !isset($values['stateId'])) {
            $lookup = (strlen($values['state']) == 2) ? 'abbreviation' : 'name';
            $state = State::find()->where([$lookup => $values['state']])->one();
            $values['stateId'] = ($state) ? $state->id : $values['stateId'] = 0;
        }
        $this->$address = new AddressForm();
        if ($address == 'shipFrom') {
            $this->$address->scenario = AddressForm::SCENARIO_FROM;
        }
        $this->$address->setAttributes($values);
        return $this->$address->validate();
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

        // Initialize and validate AddressForm object
        if (isset($this->shipTo)) {
            $allValidated = $allValidated && $this->addressStateValidation('shipTo');

        }

        if (isset($this->shipFrom)) {
            $allValidated = $allValidated && $this->addressStateValidation('shipFrom');
        }

        // Initialize and validate TrackingForm object
        if (isset($this->tracking)) {
            $values = (array)$this->tracking;
            $this->tracking = new TrackingForm();
            $this->tracking->setAttributes($values);
            if ($this->scenario == self::SCENARIO_UPDATE) {
                $allValidated = $allValidated && $this->tracking->validate();
            }
        }

        // Initialize and validate ItemForm objects
        if (isset($this->items) && is_array($this->items)) {
            $params = $this->items;
            $this->items = [];
            foreach ($params as $idx => $values) {
                $this->items[$idx] = new ItemForm();
                $this->items[$idx]->setAttributes((array)$values);
                $allValidated = $allValidated && $this->items[$idx]->validate();
            }
        }

        if (isset($this->packages) && is_array($this->packages)) {
            $params = $this->packages;

            $this->packages = [];
            foreach ($params as $idx => $values) {
                $this->packages[$idx] = new PackageForm();
                $this->packages[$idx]->setAttributes((array) $values);
                $allValidated = $allValidated && $this->packages[$idx]->validate();
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

        // shipTo
        if (is_object($this->shipTo) && $this->shipTo->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['shipTo' => $this->shipTo->getErrors()]);
        }

        // tracking
        if (is_object($this->tracking) && $this->tracking->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['tracking' => $this->tracking->getErrors()]);
        }

        // packages
        if (is_array($this->packages) && count($this->packages) > 0) {
            $packagesErrors = [];
            foreach ($this->packages as $idx => $package) {
                if ($package->hasErrors()) {
                    $packagesErrors["packages_$idx"] = $package->getErrors();
                }
            }
            if (!empty($packagesErrors)) {
                $errors = ArrayHelper::merge($errors, ['packages' => $packagesErrors]);
            }
        }

        // items
        if (is_array($this->items) && count($this->items) > 0) {
            $itemsErrors = [];
            foreach ($this->items as $idx => $item) {
                if ($item->hasErrors()) {
                    $itemsErrors["item_$idx"] = $item->getErrors();
                }
            }
            if (!empty($itemsErrors)) {
                $errors = ArrayHelper::merge($errors, ['items' => $itemsErrors]);
            }
        }

        return $errors;
    }

}