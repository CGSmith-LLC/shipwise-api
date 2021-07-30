<?php


namespace common\models\base;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table `integration_meta`
 *
 * @property int    $id
 * @property int    $integration_id
 * @property string $key
 * @property string $value
 * @property string $created_date
 */

class BaseIntegrationMeta extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
	{
        return 'integration_meta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
	{
        return [
            [['created_date'], 'safe'],
            [['key', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
	{
        return [
            'id'             => 'ID',
            'integration_id' => 'Integration ID',
            'key'            => 'Key',
            'value'          => 'Value',
            'created_date'   => 'Created Date',
        ];
    }
}