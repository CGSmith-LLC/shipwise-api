<?php


namespace common\models;

use common\adapters\ECommerceAdapter;
use common\services\BaseService;
use common\services\ShopifyService;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "integration".
 *
 * @property string $id
 * @property string $name
 * @property int $customer_id
 * @property string $ecommerce
 * @property string $fulfillment
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
        return [
            [['name', 'customer_id', 'ecommerce', 'fulfillment'],"required"],
            [['name', 'ecommerce', 'fulfillment'], 'string', 'max' => 64],
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
        $adaptername = '\\common\\adapters\\' . ucfirst($this->ecommerce) . 'Adapter';
        return new $adaptername($json, $customer_id);
    }

    /**
     * @return BaseService
     */
    public function getService(): BaseService
    {
        $interfacename = '\\common\\services\\' . ucfirst($this->ecommerce) . 'Service';

        /** @var BaseService $interface */
        $interface = new $interfacename();
        $interface->applyMeta(IntegrationMeta::findAll(['integration_id' => $this->id]));

        return $interface;
    }
}