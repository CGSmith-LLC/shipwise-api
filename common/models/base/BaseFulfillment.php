<?php


namespace common\models\base;

/**
 * This is the model class for table `fulfillment_meta`
 *
 * @property int    $id
 * @property int    $integration_id
 * @property string $key
 * @property string $value
 * @property string $created_date
 */
class BaseFulfillment extends \yii\db\ActiveRecord
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