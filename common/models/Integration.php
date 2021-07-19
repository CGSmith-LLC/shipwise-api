<?php


namespace common\models;

use common\adapters\ECommerceAdapter;
use common\interfaces\ECommerceInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "addresses".
 *
 * @property string $name
 * @property int $customer_id
 * @property string $ecommerce
 * @property ??? $metadata
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
            [['name', 'customer_id', 'ecommerce'],"required"],
            [['name', 'ecommerce'], 'string', 'max' => 64],
            [['customer_id'], 'integer'],
        ];
    }

    /** @inheritDoc */
    public function attributeLabels()
    {
        // TODO: make
        return [

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
        return new $interfacename();
    }
}