<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * Class OrderForm
 *
 * @todo This was cloned from CustomerForm. Modify it for OrderForm.
 *
 * @package api\modules\v1\models\forms
 *
 * @SWG\Definition(
 *     definition = "OrderForm",
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
class OrderForm extends Model
{
	public $name;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			["name", "required", "message" => '{attribute} is required.'],
			["name", "string", 'length' => [2, 45]],
		];
	}
}