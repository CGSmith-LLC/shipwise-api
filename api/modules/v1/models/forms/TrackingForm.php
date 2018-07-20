<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use api\modules\v1\models\core\ServiceEx;

/**
 * Class TrackingForm
 *
 * @package api\modules\v1\models\forms
 *
 * @SWG\Definition(
 *     definition = "TrackingForm",
 *     required   = { "service", "trackingNumber" },
 *     @SWG\Property(
 *            property = "serviceId",
 *            type = "integer",
 *     		  enum = {14,15,16,17,18,19,20,21,22,23,24,25,26},
 *              description = "Service ID
 						14 - FedEx Ground
 	 					15 - FedEx Express Saver
 	 					16 - FedEx 2Day
 	 					17 - FedEx 2Day AM
 						18 - FedEx First Overnight
 						19 - FedEx Priority Overnight
 						20 - FedEx Standard Overnight
 						21 - UPS Ground
						22 - UPS 3 Day Select
 						23 - UPS 2nd Day Air
 						24 - UPS 2nd Day Air AM
 						25 - UPS Next Day Air Saver
 						26 - UPS Next Day Air",
 *        ),
 *     @SWG\Property(
 *            property = "trackingNumber",
 *            type = "string",
 *            description = "Tracking number",
 *            minLength = 2,
 *            maxLength = 100
 *        ),
 * )
 */
class TrackingForm extends Model
{
	/** @var int */
	public $serviceId;
	/** @var string */
	public $trackingNumber;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['serviceId', 'trackingNumber'], 'required', 'message' => '{attribute} is required.'],
			['trackingNumber', 'string', 'length' => [2, 100]],
			['serviceId', 'integer'],
			[
				'serviceId',
				'in',
				'range'   => ServiceEx::getIdsAsArray(),
				'message' => '{attribute} value is incorrect. Valid values are: ' .
					implode(ServiceEx::getIdsAsArray(), ', '),
			],
		];
	}
}