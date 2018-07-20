<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use api\modules\v1\models\core\StateEx;

/**
 * Class AddressForm
 *
 * @package api\modules\v1\models\forms
 *
 * @SWG\Definition(
 *     definition = "AddressForm",
 *     required   = { "name", "address1", "city", "stateId", "zip" },
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Contact name",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "address1",
 *            type = "string",
 *            description = "Address line 1",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "address2",
 *            type = "string",
 *            description = "Address line 2",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *      @SWG\Property(
 *            property = "city",
 *            type = "string",
 *            description = "City",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "stateId",
 *            type = "integer",
 *     		  enum = {1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,
 *     					21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,
 *     					37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52},
 *            description = "State ID
						1 - Alabama
						2 - Alaska
						3 - Arizona
						4 - Arkansas
						5 - California
						6 - Colorado
						7 - Connecticut
						8 - Delaware
						9 - District of Columbia
						10 - Florida
						11 - Georgia
						12 - Hawaii
						13 - Idaho
						14 - Illinois
						15 - Indiana
						16 - Iowa
						17 - Kansas
						18 - Kentucky
						19 - Louisiana
						20 - Maine
						21 - Maryland
						22 - Massachusetts
						23 - Michigan
						24 - Minnesota
						25 - Mississippi
						26 - Missouri
						27 - Montana
						28 - Nebraska
						29 - Nevada
						30 - New Hampshire
						31 - New Jersey
						32 - New Mexico
						33 - New York
						34 - North Carolina
						35 - North Dakota
						36 - Ohio
						37 - Oklahoma
						38 - Oregon
						39 - Pennsylvania
						40 - Puerto Rico
						41 - Rhode Island
						42 - South Carolina
						43 - South Dakota
						44 - Tennessee
						45 - Texas
						46 - Utah
						47 - Vermont
						48 - Virginia
						49 - Washington
						50 - West Virginia
						51 - Wisconsin
						52 - Wyoming",
 *        ),
 *     @SWG\Property(
 *            property = "zip",
 *            type = "string",
 *            description = "ZIP / Postal Code",
 *            minLength = 2,
 *            maxLength = 16
 *        ),
 *     @SWG\Property(
 *            property = "phone",
 *            type = "string",
 *            description = "Phone number",
 *            minLength = 2,
 *            maxLength = 32
 *        ),
 *     @SWG\Property(
 *            property = "notes",
 *            type = "string",
 *            description = "Notes",
 *            minLength = 2,
 *            maxLength = 140
 *        ),
 * )
 */
class AddressForm extends Model
{
	/** @var string */
	public $name;
	/** @var string */
	public $address1;
	/** @var string */
	public $address2;
	/** @var string */
	public $city;
	/** @var int */
	public $stateId;
	/** @var string */
	public $zip;
	/** @var string */
	public $phone;
	/** @var string */
	public $notes;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'address1', 'city', 'stateId', 'zip'], 'required', 'message' => '{attribute} is required.'],
			[['name', 'address1', 'address2', 'city'], 'string', 'length' => [2, 64]],
			['zip', 'string', 'length' => [2, 16]],
			['phone', 'string', 'length' => [2, 32]],
			['notes', 'string', 'length' => [2, 140]],
			['stateId', 'integer'],
			[
				'stateId',
				'in',
				'range'   => StateEx::getIdsAsArray(),
				'message' => '{attribute} value is incorrect. Valid values are: ' .
					implode(StateEx::getIdsAsArray(), ', '),
			],
		];
	}
}