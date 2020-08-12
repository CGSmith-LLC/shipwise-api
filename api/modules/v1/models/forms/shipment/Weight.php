<?php

namespace api\modules\v1\models\forms\shipment;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "Weight",
 *     required   = { "units", "value" },
 *     @SWG\Property(
 *            property = "units",
 *            type = "string",
 *            enum = {"LB","KG"},
 *            description = "Weight units"
 *        ),
 *      @SWG\Property(
 *            property = "value",
 *            type = "number",
 *            format = "double",
 *            description = "Weight value"
 *        )
 * )
 */

/**
 * Class Weight
 *
 * @package api\modules\v1\models\forms\shipment
 *
 * @property string $units
 * @property double $value
 */
class Weight extends Model
{

    const UNITS_LB = 'LB';
    const UNITS_KG = 'KG';

    protected static $unitsTypes = [
        self::UNITS_LB => self::UNITS_LB,
        self::UNITS_KG => self::UNITS_KG,
    ];

    /**
     * Weight units
     * @see Weight::$unitsTypes for list of codes
     *
     * @var string
     */
    public $units;

    /**
     * Weight value
     * @var double
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['units', 'value'], 'required', 'message' => 'Weight {attribute} is required.'],
            [
                'units',
                'in',
                'range'   => self::$unitsTypes,
                'message' => 'Weight {attribute} value is incorrect. Valid values are: ' .
                    implode(', ', self::$unitsTypes),
            ],
            ['value', 'double', 'min' => 0.01],
        ];
    }
}