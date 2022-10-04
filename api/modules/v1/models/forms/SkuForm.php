<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "SkuForm",
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
 *            description = "Name of the SKU",
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "excluded",
 *            type = "boolean",
 *            description = "Whether or not this SKU is excluded from fulfillment"
 *        ),
 * )
 */

/**
 * Class SkuForm
 *
 * @package api\modules\v1\models\forms
 */
class SkuForm extends Model
{

    /** @var integer */
    public $id;
    /** @var integer */
    public $customer_id;
    /** @var string */
    public $sku;
    /** @var string */
    public $name;
    /** @var bool */
    public $excluded;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'sku'], 'required', 'message' => '{attribute} is required.'],
            [['customer_id', 'id', 'excluded'], 'integer'],
            [['sku'], 'string', 'max' => 16],
            [['name', 'substitute_1', 'substitute_2', 'substitute_3'], 'string', 'max' => 64],
        ];
    }
}
