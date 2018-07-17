<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * Class CustomerForm
 *
 * @package api\modules\v1\models\forms
 *
 * @SWG\Definition(
 *     definition = "CustomerForm",
 *     required   = { "name" },
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Customer name",
 *            minLength = 2,
 *            maxLength = 45
 *        ),
 * )
 */
class CustomerForm extends Model
{
	const SUCCESS            = 1;
	const ERR_MISSING_FIELDS = 2;

	public $name;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			["name", "required", "message" => 'Field "{attribute}" is required.'],
			["name", "string", 'length' => [2, 45]],
		];
	}
}