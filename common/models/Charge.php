<?php

namespace common\models;

use yii\base\Model;

/**
 * Class Charge
 *
 * @package common\models
 *
 * @property string $type
 * @property string $description
 * @property Money  $amount
 */
class Charge extends Model
{

    /** @var string */
    public $type;

    /** @var string */
    public $description;

    /** @var Money */
    public $amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'amount'], 'required', 'message' => '{attribute} is required.'],
            ['amount', 'double'],
            ['type', 'string', 'length' => [2, 60]],
            ['description', 'string', 'length' => [2, 100]],
        ];
    }
}
