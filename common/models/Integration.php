<?php


namespace common\models;

use common\adapters\ECommerceAdapter;
use common\interfaces\ECommerceInterface;
use yii\base\BaseObject;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "integration".
 *
 * @property string $name
 * @property int $customer_id
 * @property string $ecommerce
 * @property BaseObject $metadata
 *      Metadata includes:
 *          API Key
 *          API Secret
 *          API URL
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
            'metadata' => 'Metadata'
        ];
    }

    public function getAdapter($json, $customer_id): ECommerceAdapter
    {
        $adaptername = ucfirst($this->ecommerce) . 'Adapter';
        return new $adaptername($json, $customer_id);
    }

    public function getInterface(): ECommerceInterface
    {
        $interfacename = ucfirst($this->ecommerce) . 'Interface';
        return new $interfacename(/*This needs the API key, secret, and URL */);
    }
}