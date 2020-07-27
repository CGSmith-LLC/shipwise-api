<?php

namespace api\modules\v1\models\forms;

use common\models\State;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\order\StatusEx;

/**
 * @SWG\Definition(
 *     definition = "HistoryForm",
 *     required   = {"comment" },
 *     @SWG\Property(
 *            property = "comment",
 *            type = "text",
 *            description = "Comment of what the change was.",
 *        ),
 * )
 */

/**
 * Class OrderForm
 *
 * @package api\modules\v1\models\forms
 */
class HistoryForm extends Model
{

    /** @var string $comment*/
    public $comment;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['comment'],
                'required',
                'message' => '{attribute} is required.',
            ]
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