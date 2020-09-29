<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *     definition = "InventoryForm",
 *     required   = { "sku" },
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
 *            property = "available_quantity",
 *            type = "number",
 *            description = "available quantity",
 *        ),
 *      @SWG\Property(
 *            property = "location",
 *            type = "string",
 *            description = "Location that the warehouse designates where the inventory is located",
 *            maxLength = 64
 *        ),
 * )
 */

/**
 * Class InventoryForm
 *
 * @package api\modules\v1\models\forms
 */
class InventoryForm extends Model
{

    /** @var integer */
    public $id;
    /** @var float */
    public $available_quantity;
    /** @var string */
    public $sku;
    /** @var string */
    public $name;
    /** @var integer */
    public $customer_id;
    /** @var string */
    public $location;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'sku'], 'required', 'message' => '{attribute} is required.'],
            [['customer_id', 'id'], 'integer'],
            [['name', 'sku', 'location'], 'string', 'max' => 64],
            [['available_quantity'], 'number']
        ];
    }
}
