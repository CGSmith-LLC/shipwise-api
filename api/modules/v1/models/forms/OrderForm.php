<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\order\StatusEx;

/**
 * @SWG\Definition(
 *     definition = "OrderForm",
 *     required   = { "customerReference", "shipTo", "items" },
 *     @SWG\Property(
 *            property = "uuid",
 *            type = "string",
 *            description = "Unique identifier from ecommerce platform",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "orderReference",
 *            type = "string",
 *            description = "Order reference - typically Order Number from ecommerce side",
 *            minLength = 1,
 *            maxLength = 45
 *        ),
 *     @SWG\Property(
 *            property = "customerReference",
 *            type = "string",
 *            description = "Customer reference",
 *            minLength = 1,
 *            maxLength = 64
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
 *            enum = {1,9},
 *            default = "9",
 *            description = "Order status
					1 - Shipped
					9 - Open",
 *       ),
 *     @SWG\Property(
 *            property = "items",
 *            type = "array",
 *     		  @SWG\Items( ref = "#/definitions/ItemForm" )
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
	const SCENARIO_UPDATE  = 'update';  // the update scenario
	const SCENARIO_DELETE  = 'delete';  // the delete scenario

	/** @var string */
	public $uuid;
	/** @var string */
	public $orderReference;
	/** @var string */
	public $customerReference;
	/** @var AddressForm */
	public $shipTo;
	/** @var TrackingForm */
	public $tracking;
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
				['orderReference', 'customerReference', 'shipTo', 'items'],
				'required', 'message' => '{attribute} is required.',
			],
			['uuid', 'string', 'length' => [1, 64]],
			['orderReference', 'string', 'length' => [1, 45]],
			['customerReference', 'string', 'length' => [1, 64]],
			['tracking', 'required', 'on' => self::SCENARIO_UPDATE, 'message' => '{attribute} is required.'],
			['status', 'required', 'on' => self::SCENARIO_UPDATE, 'message' => '{attribute} is required.'],
			['status', 'integer', 'on' => self::SCENARIO_UPDATE],
			[
				'status',
				'in',
				'range'   => StatusEx::getIdsAsArray(),
				'message' => '{attribute} value is incorrect. Valid values are: ' .
					implode(StatusEx::getIdsAsArray(), ', '),
			],
			['items', 'checkIsArray'],
			['tracking', 'safe'],
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
	 * @see getErrorsAll()
	 *
	 * @return bool whether the validation is successful without any error.
	 */
	public function validateAll()
	{
		// Validate this model
		$allValidated = $this->validate();

		// Initialize and validate AddressForm object
		if (isset($this->shipTo)) {
			$values       = (array)$this->shipTo;
			$this->shipTo = new AddressForm();
			$this->shipTo->setAttributes($values);
			$allValidated = $allValidated && $this->shipTo->validate();
		}

		// Initialize and validate TrackingForm object
		if (isset($this->tracking)) {
			$values         = (array)$this->tracking;
			$this->tracking = new TrackingForm();
			$this->tracking->setAttributes($values);
			if ($this->scenario == self::SCENARIO_UPDATE) {
				$allValidated = $allValidated && $this->tracking->validate();
			}
		}

		// Initialize and validate ItemForm objects
		if (isset($this->items) && is_array($this->items)) {
			$params      = $this->items;
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