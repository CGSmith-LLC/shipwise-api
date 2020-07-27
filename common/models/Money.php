<?php

namespace common\models;

use yii\base\Model;

/**
 * Class Money
 *
 * @package common\models
 *
 * @property double $amount
 * @property string $currency  Currency code in ISO 4217
 */
class Money extends Model
{

    /** @var double */
    public $amount;

    /** @var string */
    public $currency;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'currency'], 'required', 'message' => '{attribute} is required.'],
            ['amount', 'double'],
            ['currency', 'string', 'length' => 3],
        ];
    }
}
