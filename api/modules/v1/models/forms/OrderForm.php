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
 *            maxLength = 140
 *        ),
 *     @SWG\Property(
 *            property = "requestedShipDate",
 *            type = "string",
 *            description = "When the order should ship and be fulfilled",
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
 *            enum = {1,7,8,9,10,11},
 *            default = "9",
 *            description = "Order status
 *                    1  - Shipped
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
 *            property = "notes",
 *            type = "string",
 *            description = "Order notes",
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
    /** @var AddressForm */
    public $shipTo;
    /** @var TrackingForm */
    public $tracking;
    /** @var PackageForm[] */
    public $packages;
    /** @var string */
    public $status;
    /** @var ItemForm[] */
    public $items;


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
            [['poNumber', 'uuid', 'origin', 'customerReference', 'notes'], 'string', 'length' => [1, 64]],
            ['orderReference', 'string', 'length' => [1, 45]],
            ['notes', 'string', 'length' => [1, 140]],
            ['requestedShipDate', 'date', 'format' => 'php:Y-m-d'],
            ['status', 'required', 'on' => self::SCENARIO_UPDATE, 'message' => '{attribute} is required.'],
            ['status', 'integer', 'on' => self::SCENARIO_UPDATE],
            [
                'status',
                'in',
                'range' => StatusEx::getIdsAsArray(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(StatusEx::getIdsAsArray(), ', '),
            ],
            [['items', 'packages'], 'checkIsArray'],
            [['tracking', 'packages', 'service_id', 'carrier_id'], 'safe'],
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
            $values = (array)$this->shipTo;
            // If stateId is not set but state exists then try to reference the state's value in the DB
            if (isset($values['state']) && !isset($values['stateId'])) {
                $lookup = (strlen($values['state']) == 2) ? 'abbreviation' : 'name';
                $state = State::find()->where([$lookup => $values['state']])->one();
                $values['stateId'] = $state->id;
            }
            $this->shipTo = new AddressForm();
            $this->shipTo->setAttributes($values);
            $allValidated = $allValidated && $this->shipTo->validate();
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