<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "AliasForm",
 *     required   = { "sku", "name", "children" },
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
 *             property = "children",
 *             type = "array",
 *             @SWG\Items( ref = "#/definitions/AliasChildForm" )
 *         ),
 *      @SWG\Property(
 *            property = "location",
 *            type = "string",
 *            description = "Location that the warehouse designates where the inventory is located",
 *            maxLength = 64
 *        ),
 * )
 */

/**
 * Class AliasForm
 *
 * @package api\modules\v1\models\forms
 */
class AliasForm extends Model
{

    /** @var integer */
    public $id;

    /** @var integer */
    public $customer_id;

    /** @var string */
    public $sku;

    /** @var string */
    public $name;

    /** @var array */
    public $children = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'name', 'sku', 'children'], 'required', 'message' => '{attribute} is required.'],
            [['children'], 'checkIsArray'],
            [['sku'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * Custom validator
     * Checks if attribute is an array and has at least one item
     *
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function checkIsArray($attribute, $params, $validator)
    {
        if (!(is_array($this->$attribute) && count($this->$attribute) > 0)) {
            $this->addError($attribute, '{attribute} must be an array and have at least one item.');
        }
    }
}
