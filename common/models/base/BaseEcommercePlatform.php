<?php

namespace common\models\base;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "ecommerce_platform".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 */
class BaseEcommercePlatform extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'ecommerce_platform';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['meta'], 'default', 'value' => null],
            [['name'], 'required'],
            [['status'], 'integer'],
            [['meta'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Platform',
            'status' => 'Status',
            'meta' => 'Meta Data',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
