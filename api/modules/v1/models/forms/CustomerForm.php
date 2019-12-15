<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "CustomerForm",
 *     required   = { "name" },
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Customer name",
 *            minLength = 2,
 *            maxLength = 45
 *        ),
 * )
 */

/**
 * Class CustomerForm
 *
 * @package api\modules\v1\models\forms
 */
class CustomerForm extends Model
{

    public $name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required', 'message' => '{attribute} is required.'],
            ['name', 'string', 'length' => [2, 45]],
        ];
    }
}