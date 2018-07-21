<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "ItemForm",
 *     required   = { "quantity", "sku" },
 *     @SWG\Property(
 *            property = "quantity",
 *            type = "integer",
 *            description = "Quantity"
 *        ),
 *     @SWG\Property(
 *            property = "sku",
 *            type = "string",
 *            description = "SKU",
 *            minLength = 2,
 *            maxLength = 45
 *        ),
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Item name",
 *            minLength = 2,
 *            maxLength = 45
 *        ),
 * )
 */

/**
 * Class ItemForm
 *
 * @package api\modules\v1\models\forms
 */
class ItemForm extends Model
{
	/** @var int */
	public $quantity;
	/** @var string */
	public $sku;
	/** @var string */
	public $name;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['quantity', 'sku'], 'required', 'message' => '{attribute} is required.'],
			['quantity', 'integer'],
			['quantity', 'compare', 'compareValue' => 0, 'operator' => '>'],
			[['sku', 'name'], 'string', 'length' => [2, 45]],
		];
	}
}