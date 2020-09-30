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
 *            maxLength = 16
 *        ),
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Name of the SKU",
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "substitute_1",
 *            type = "string",
 *            description = "Substitution for SKU",
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "substitute_2",
 *            type = "string",
 *            description = "Substitution for SKU",
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "substitute_3",
 *            type = "string",
 *            description = "Substitution for SKU",
 *            maxLength = 64
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
    /** @var string */
    public $substitute_1;
    /** @var string */
    public $substitute_2;
    /** @var string */
    public $substitute_3;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'sku'], 'required', 'message' => '{attribute} is required.'],
            [['customer_id', 'id'], 'integer'],
            [['sku'], 'string', 'max' => 16],
            [['name', 'substitute_1', 'substitute_2', 'substitute_3'], 'string', 'max' => 64],
        ];
    }
}
