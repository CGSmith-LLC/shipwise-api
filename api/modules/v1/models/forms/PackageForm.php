<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use api\modules\v1\models\core\ServiceEx;

/**
 * @SWG\Definition(
 *     definition = "PackageForm",
 *     required   = { "service", "trackingNumber" },
 *     @SWG\Property(
 *            property = "serviceId",
 *            type = "integer",
 *        ),
 *     @SWG\Property(
 *            property = "trackingNumber",
 *            type = "string",
 *            description = "Tracking number",
 *            minLength = 2,
 *            maxLength = 100
 *        ),
 * )
 */

/**
 * Class PackageForm
 *
 * @package api\modules\v1\models\forms
 */
class PackageForm extends Model
{

    public $length;
    public $height;
    public $width;
    public $weight;
    public $tracking;
    public $package_items;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['tracking', 'string', 'length' => [2, 100]],
            [['length', 'height', 'width', 'weight','package_items'], 'safe'],
        ];
    }
}