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
 *     @SWG\Property(
 *            property = "notes",
 *            type = "string",
 *            description = "Item notes",
 *            minLength = 1,
 *            maxLength = 64
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
    /** @var int */
    public $alias_quantity;
    /** @var string */
    public $alias_sku;
    /** @var string */
    public $notes;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quantity', 'sku'], 'required', 'message' => '{attribute} is required.'],
            [['quantity', 'alias_quantity'], 'integer'],
            ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['sku', 'alias_sku', 'uuid', 'notes'], 'string', 'length' => [1, 64]],
            ['name', 'string', 'length' => [1, 128]],
        ];
    }
}