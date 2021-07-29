<?php


namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table `fulfillment_meta`
 *
 * @property int 	$id
 * @property int 	$fulfillment_id
 * @property string $key
 * @property string $value
 * @property string $created_date
 */

class BaseFulfillmentMeta extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'fulfillment_meta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_date'], 'safe'],
            [['key', 'value'], 'string', 'max' => 127],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'fulfillment_id' => 'Integration ID',
            'key'            => 'Key',
            'value'          => 'Value',
            'created_date'   => 'Created Date',
        ];
    }
}