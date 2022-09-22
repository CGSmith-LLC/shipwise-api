<?php

namespace frontend\models\forms\behaviors;

use common\models\forms\BaseForm;

/** @property string $dropDownName */
class ModifyQuantityForm extends BaseForm
{
    public static string $dropDownName = 'Modify Quantity';

    public $item;
    public $jsonToCheck;
    public $jsonValue;
    public $multiplier;

    public function rules()
    {
        return [
            [['item', 'jsonToCheck', 'jsonValue', 'multiplier'], 'required'],
            [['item', 'jsonToCheck', 'jsonValue'], 'string'],
            [['multiplier'], 'integer'],
            [['multiplier'], 'default', 'value' => '1'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'item' => 'SKU to check metadata for',
            'jsonToCheck' => 'JSON metadata to compare',
            'jsonValue' => 'JSON value we are looking for',
            'multiplier' => 'Amount to multiply current quantity by',
        ];
    }

}