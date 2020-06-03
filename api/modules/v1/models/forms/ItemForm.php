<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "ItemForm",
 *     required   = { "quantity", "sku" },
 *     @SWG\Property(
 *            property = "uuid",
 *            type = "string",
 *            description = "UUID of item from ecommerce system"
 *        ),
 *     @SWG\Property(
 *            property = "quantity",
 *            type = "integer",
 *            description = "Quantity"
 *        ),
 *     @SWG\Property(
 *            property = "sku",
 *            type = "string",
 *            description = "SKU",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Item name",
 *            minLength = 2,
 *            maxLength = 128
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

    /** @var string */
    public $uuid;
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
            ['uuid', 'string', 'length' => [1, 64]],
            ['sku', 'string', 'length' => [1, 64]],
        ];
    }
}