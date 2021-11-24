<?php

namespace common\models;

use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

/**
 * This is the model class for table "integration_hookdeck".
 *
 * @property int $id
 * @property int $integration_id
 * @property string $source_name
 * @property string $source_id
 * @property string $source_url
 * @property string $destination_name
 * @property string $destination_id
 * @property string $destination_url
 * @property string $created_at
 * @property string $updated_at
 */
class IntegrationHookdeck extends \yii\db\ActiveRecord
{
    /**
     * @var mixed|Client|null
     */
    public mixed $client;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integration_hookdeck';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['integration_id', 'source_name', 'source_id', 'source_url', 'destination_name', 'destination_id', 'destination_url'], 'required'],
            [['integration_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['source_name', 'destination_name'], 'string', 'max' => 25],
            [['source_id', 'source_url', 'destination_id', 'destination_url'], 'string', 'max' => 255],
        ];
    }

    public function init()
    {
        parent::init();

        // Set API Key for Hookdeck
        $this->client = new Client([
            'baseUrl' => 'https://api.hookdeck.com/2021-08-01',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'parsers' => [
                // configure options of the JsonParser, parse JSON as array
                Client::FORMAT_JSON => [
                    'class' => 'yii\httpclient\JsonParser',
                    'asArray' => true,
                ]
            ],
        ]);

        // Setup event for auth before each send
        $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
            $event->request->addHeaders(['Authorization' => 'Basic ' . base64_encode(Yii::$app->params['hookdeckApiKey'] . ':')]);
        });

    }

    /**
     * Saves the following:
     *   1. Hookdeck connection (source and destination)
     *   2. Saves in DB for reference by WebhookController (or anyone else)
     *   3. Pass a bool back to the caller so it can proceed with upstream calls (WooCommerce/BigCommerce)
     *
     * @param bool $insert
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function beforeSave($insert)
    {
        $connectionResponse = $this->client->createRequest()
            ->setUrl('/connections')
            ->setMethod('POST')
            ->setData([
                'name' => 'integration-' . $this->integration_id,
                'source' => [
                    'name' => $this->source_name,
                ],
                'destination' => [
                    'name' => Yii::$app->params['hookdeckDestinationName'],
                ]
            ])
            ->send();
        // @todo save response approriately
        $sourceData = $connectionResponse->getData();
        $this->source_id = $sourceData['source']['id'];
        $this->source_name = $sourceData['source']['name'];
        $this->source_url = $sourceData['source']['url'];
        $this->destination_id = $sourceData['destination']['id'];
        $this->destination_name = $sourceData['destination']['name'];
        $this->destination_url = $sourceData['destination']['url'];
        $this->validate();

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'integration_id' => Yii::t('app', 'Integration ID'),
            'source_name' => Yii::t('app', 'Source Name'),
            'source_id' => Yii::t('app', 'Source ID'),
            'source_url' => Yii::t('app', 'Source Url'),
            'destination_name' => Yii::t('app', 'Destination Name'),
            'destination_id' => Yii::t('app', 'Destination ID'),
            'destination_url' => Yii::t('app', 'Destination Url'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
