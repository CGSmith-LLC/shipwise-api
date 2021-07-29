<?php


namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table `fulfillment`
 *
 * @property int $id;
 * @property string $name;
 */
class BaseFulfillment extends ActiveRecord
{
    /** @inheritDoc */
    public static function tableName()
    {
        return "fulfillment";
    }

    /** @inheritDoc */
    public function rules()
    {
        return [
            [['name', ],"required"],
            [['name',] , 'string', 'max' => 255],
        ];
    }

    /** @inheritDoc */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}