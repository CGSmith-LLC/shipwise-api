<?php

namespace api\modules\v1\models\forms\shipment;

use common\models\shipping\PackageType;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *     definition = "Package",
 *     required   = { "type" },
 *     @SWG\Property(
 *            property = "type",
 *            type = "string",
 *            enum = {"MyPackage","Envelope","Pak"},
 *            description = "Type of packaging"
 *        ),
 *
 *      @SWG\Property(
 *            property = "weight",
 *            ref = "#/definitions/Weight"
 *       ),
 *
 *     @SWG\Property(
 *            property = "dimensions",
 *            ref = "#/definitions/Dimensions"
 *       ),
 * )
 */

/**
 * Class Package
 *
 * @package api\modules\v1\models\forms\shipment
 *
 * @property string     $type
 * @property Weight     $weight
 * @property Dimensions $dimensions
 */
class Package extends Model
{

    /**
     * Package type
     * @see PackageType::getList() for list of codes
     *
     * @var string
     */
    public $type;

    /**
     * Package weight
     * @var Weight
     */
    public $weight;

    /**
     * Package dimensions
     * @var Dimensions
     */
    public $dimensions;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['type', 'required', 'message' => 'Package {attribute} is required.'],
            [
                ['weight', 'dimensions'],
                'required',
                'when'    => function ($model) {
                    return $model->type == PackageType::MY_PACKAGE;
                },
                'message' => 'Package {attribute} is required.',
            ],
            [
                'type',
                'in',
                'range'   => PackageType::getList(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', PackageType::getList()),
            ],
        ];
    }

    /**
     * Performs data validation for this model and its related models
     *
     * Errors found during the validation can be retrieved via getErrorsAll()
     *
     * @return bool whether the validation is successful without any error.
     * @see getErrorsAll()
     *
     */
    public function validateAll()
    {
        // Validate this model
        $allValidated = $this->validate();

        // weight: initialize and validate Weight object
        if (isset($this->weight)) {
            $values       = (array)$this->weight;
            $this->weight = new Weight();
            $this->weight->setAttributes($values);
            $allValidated = $allValidated && $this->weight->validate();
        }

        // dimensions: initialize and validate Dimensions object
        if (isset($this->dimensions)) {
            $values           = (array)$this->dimensions;
            $this->dimensions = new Dimensions();
            $this->dimensions->setAttributes($values);
            $allValidated = $allValidated && $this->dimensions->validate();
        }

        return $allValidated;
    }

    /**
     * Returns the errors for all attributes of this model and its related models
     *
     * @return array
     */
    public function getErrorsAll()
    {
        $errors = $this->getErrors();

        // weight
        if (is_object($this->weight) && $this->weight->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['weight' => $this->weight->getErrors()]);
        }

        // dimensions
        if (is_object($this->dimensions) && $this->dimensions->hasErrors()) {
            $errors = ArrayHelper::merge($errors, ['dimensions' => $this->dimensions->getErrors()]);
        }

        return $errors;
    }
}