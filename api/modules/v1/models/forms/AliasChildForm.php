<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "AliasChildForm",
 *     required   = { "sku", "name", "quantity" },
 *
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
 *            description = "name",
 *            maxLength = 128
 *        ),
 *      @SWG\Property(
 *             property = "quantity",
 *             type = "integer",
 *             description = "Quantity"
 *         )
 * )
 */

/**
 * Class AliasChildForm
 *
 * @package api\modules\v1\models\forms
 */
class AliasChildForm extends Model
{

    /** @var string */
    public $sku;
    /** @var string */
    public $name;
    /** @var integer */
    public $quantity;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'sku', 'quantity'], 'required', 'message' => '{attribute} is required.'],
            [['quantity'], 'integer'],
            [['sku'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128]
        ];
    }
}
