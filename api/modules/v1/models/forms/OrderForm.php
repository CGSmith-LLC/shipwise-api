<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use api\modules\v1\models\order\StatusEx;

/**
 * Class OrderForm
 *
 * @package api\modules\v1\models\forms
 *
 * @SWG\Definition(
 *     definition = "OrderForm",
 *     required   = { "orderReference", "customerReference", "shipTo" },
 *     @SWG\Property(
 *            property = "orderReference",
 *            type = "string",
 *            description = "Order reference",
 *            minLength = 2,
 *            maxLength = 45
 *        ),
 *     @SWG\Property(
 *            property = "customerReference",
 *            type = "string",
 *            description = "Customer reference",
 *            minLength = 2,
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
 * )
 */
class OrderForm extends Model
{
	const SCENARIO_DEFAULT = 'default'; // the create scenario
	const SCENARIO_UPDATE  = 'update';  // the update scenario
	const SCENARIO_DELETE  = 'delete';  // the delete scenario

	/** @var string */
	public $orderReference;
	/** @var string */
	public $customerReference;
	/** @var AddressForm */
	public $shipTo;
	/** @var TrackingForm */
	public $tracking;
	/** @var @todo */
	//public $items;
	/** @var string */
	public $status;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['orderReference', 'customerReference', 'shipTo'], 'required', 'message' => '{attribute} is required.'],
			['orderReference', 'string', 'length' => [2, 45]],
			['customerReference', 'string', 'length' => [2, 64]],
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
		];
	}

	/**
	 * Performs the data validation for this model and its related models
	 *
	 * Errors found during the validation can be retrieved via getErrors()
	 *
	 * @return bool whether the validation is successful without any error.
	 */
	public function validateAll()
	{
		$shipToValidated = $trackingValidated = true; // Initial value

		// Validate this model
		$orderValidated = $this->validate();

		// Initialize and validate AddressForm object
		if (isset($this->shipTo)) {
			$shipToValues = (array)$this->shipTo;
			$this->shipTo = new AddressForm();
			$this->shipTo->setAttributes($shipToValues);
			$shipToValidated = $this->shipTo->validate();
		}

		// Initialize and validate TrackingForm object
		if (isset($this->tracking) && $this->scenario == self::SCENARIO_UPDATE) {
			$trackingValues = (array)$this->tracking;
			$this->tracking = new TrackingForm();
			$this->tracking->setAttributes($trackingValues);
			$trackingValidated = $this->tracking->validate();
		}

		// \yii\helpers\VarDumper::dump($shipTo->attributes);exit;

		return ($orderValidated && $shipToValidated && $trackingValidated);
	}

}