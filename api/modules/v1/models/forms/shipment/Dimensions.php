<?php

namespace api\modules\v1\models\forms\shipment;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "Dimensions",
 *     required   = { "units", "length", "width", "height" },
 *     @SWG\Property(
 *            property = "units",
 *            type = "string",
 *            enum = {"IN","CM"},
 *            description = "Dimension units"
 *        ),
 *      @SWG\Property(
 *            property = "length",
 *            type = "number",
 *            format = "double",
 *            description = "Length"
 *        ),
 *     @SWG\Property(
 *            property = "width",
 *            type = "number",
 *            format = "double",
 *            description = "Width"
 *        ),
 *     @SWG\Property(
 *            property = "height",
 *            type = "number",
 *            format = "double",
 *            description = "Height"
 *        )
 * )
 */

/**
 * Class Dimensions
 *
 * @package api\modules\v1\models\forms\shipment
 *
 * @property string $units
 * @property double $length
 * @property double $width
 * @property double $height
 */
class Dimensions extends Model
{

    const UNITS_IN = 'IN';
    const UNITS_CM = 'CM';

    protected static $unitsTypes = [
        self::UNITS_IN => self::UNITS_IN,
        self::UNITS_CM => self::UNITS_CM,
    ];

    /**
     * Dimensions units
     * @see Dimensions::$unitsTypes for list of codes
     *
     * @var string
     */
    public $units;

    /** @var double */
    public $length;

    /** @var double */
    public $width;

    /** @var double */
    public $height;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['units', 'length', 'width', 'height'], 'required', 'message' => 'Dimensions {attribute} is required.'],
            [
                'units',
                'in',
                'range'   => self::$unitsTypes,
                'message' => 'Dimensions {attribute} value is incorrect. Valid values are: ' .
                    implode(self::$unitsTypes, ', '),
            ],
            [['length', 'width', 'height'], 'double', 'min' => 0.01],
        ];
    }
}