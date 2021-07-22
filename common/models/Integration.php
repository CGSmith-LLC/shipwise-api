<?php


namespace common\models;

use common\adapters\ECommerceAdapter;
use common\interfaces\ECommerceInterface;
use yii\httpclient\Client;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "integration".
 *
 * @property string $id
 * @property string $name
 * @property int $customer_id
 * @property string $ecommerce
 */

class Integration extends ActiveRecord
{
    /** @inheritDoc */
    public static function tableName()
    {
        return "integration";
    }

    /** @inheritDoc */
    public function rules()
    {
        // TODO: How do metadata?

        return [
            [['name', 'customer_id', 'ecommerce'],"required"],
            [['name', 'ecommerce'], 'string', 'max' => 64],
            [['customer_id'], 'integer'],
        ];
    }

    /** @inheritDoc */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'customer_id' => 'Customer ID',
            'ecommerce' => 'eCommerce Site',
        ];
    }

    public function getAdapter($json, $customer_id): ECommerceAdapter
    {
        $adaptername = ucfirst($this->ecommerce) . 'Adapter';
        return new $adaptername($json, $customer_id);
    }

    public function getInterface(): ECommerceInterface
    {
        $interfacename = '\\common\\interfaces\\' . ucfirst($this->ecommerce) . 'Interface';
        $metadata = [];

        foreach(IntegrationMeta::findAll(['integration_id' => $this->customer_id]) as $metadatum)
        {
            $key   = \Yii::$app->getSecurity()->decryptByKey(base64_decode($metadatum->key),   \Yii::$app->params['integrationSecret']);
            $value = \Yii::$app->getSecurity()->decryptByKey(base64_decode($metadatum->value), \Yii::$app->params['integrationSecret']);
            $metadata[$key] = $value;
        }


        $client = new Client(['baseUrl'=>$metadata['url']]);
        $auth = $metadata['api_key'] . ':' . $metadata['api_secret'];

        return new $interfacename(['client' => $client, 'auth' => $auth]);
    }
}