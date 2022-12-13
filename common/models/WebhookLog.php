<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is the model class for table "webhook_log".
 *
 * @property int $id
 * @property int $webhook_id
 * @property int $status_code
 * @property string $response
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Webhook $webhook
 */
class WebhookLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webhook_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['webhook_id', 'status_code'], 'required'],
            [['webhook_id', 'status_code', 'created_at', 'updated_at'], 'integer'],
            [['response'], 'string'],
            [['webhook_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webhook::class, 'targetAttribute' => ['webhook_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'webhook_id' => 'Webhook ID',
            'status_code' => 'Status Code',
            'response' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebhook()
    {
        return $this->hasOne(Webhook::class, ['id' => 'webhook_id']);
    }

    public function getLabelFor($attribute) {
        if ($attribute === 'status_code') {
            $label = match (substr($this->$attribute, 0, 1)) {
                '2' => 'success',
                '3' => 'warning',
                '4', '5' => 'danger',
                default => 'info',
            };
            return '<span class="label label-'.$label.'">' . $this->$attribute . '</span>';
        }

        return '';
    }

    public function getModalForView()
    {
        // just display the response if it is short
        if (strlen($this->response) < 25) {
            return $this->response;
        }

        //$json = Json::decode($this->response); // not sure if i need to do this? only way to pretty print
        $json = json_decode($this->response);
        Modal::begin([
            'id' => 'event-' . $this->id,
            'header' => 'Event ' . $this->id . ' (' . $this->status_code . ') - ' . $this->webhook->endpoint,
        ]);
        echo '<pre>'.json_encode($json, JSON_PRETTY_PRINT).'</pre>';

        Modal::end();

        return Html::button('View Last Response', [
            'data-toggle' => 'modal',
            'data-target' => '#event-' . $this->id,
            'class' => 'btn btn-sm btn-primary',
        ]);
    }
}
