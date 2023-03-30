<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\order\StatusEx;

/**
 * @SWG\Definition(
 *     definition = "Status",
 *     required   = { "status" },
 *     @SWG\Property(
 *            property = "status",
 *            type = "integer",
 *            enum = {1,2,6,7,8,9,10,11},
 *            default = "9",
 *            description = "Order status
 *                    1  - Shipped
 *                    2  - Amazon Prime
 *                    6  - On Hold
 *                    7  - Cancelled
 *                    8  - Pending Fulfillment
 *                    9  - Open
 *                    10 - WMS Error
 *                    11 - Completed",
 *       )
 * )
 */

/**
 * Class StatusForm
 *
 * @package api\modules\v1\models\forms
 */
class StatusForm extends Model
{

    final const SCENARIO_DEFAULT = 'default'; // the create scenario


    /** @var string */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['status'],
                'required',
                'message' => '{attribute} is required.',
            ],
            ['status', 'integer'],
            [
                'status',
                'in',
                'range' => StatusEx::getIdsAsArray(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', StatusEx::getIdsAsArray()),
            ],
        ];
    }
}