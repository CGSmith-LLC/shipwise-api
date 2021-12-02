<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "integration_webhook".
 *
 * @property int $id
 * @property int $integration_id
 * @property int $integration_hookdeck_id
 * @property string $source_uuid
 * @property string $name
 * @property string $topic
 * @property string $created_at
 * @property string $updated_at
 */
class IntegrationWebhook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integration_webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['integration_id', 'integration_hookdeck_id', 'source_uuid', 'name', 'topic'], 'required'],
            [['integration_id', 'integration_hookdeck_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['topic', 'source_uuid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'integration_id' => Yii::t('app', 'Integration ID'),
            'integration_hookdeck_id' => Yii::t('app', 'Integration Hookdeck ID'),
            'source_uuid' => Yii::t('app', 'Upstream UUID'),
            'name' => Yii::t('app', 'Name'),
            'topic' => Yii::t('app', 'Topic'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
