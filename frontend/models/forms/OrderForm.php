<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use frontend\models\{Order, Address, Item};

/**
 * Class OrderForm
 *
 * This model is behind Order interface
 * it handles Order model, its `hasone` Address relation, and `hasmany` Item relation.
 *
 * @property Order   $order
 * @property Address $address
 * @property Item[]  $items
 *
 * @package frontend\models\forms
 */
class OrderForm extends Model
{

    /** @var Order */
    protected $_order;

    /** @var Address */
    protected $_address;

    /** @var Item[] */
    protected $_items;

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['Order', 'Address', 'Items'], 'required'],
        ];
    }

    /** {@inheritdoc} */
    public function afterValidate()
    {
        if (!Model::validateMultiple($this->getAllModels())) {
            $this->addError(null); // add an empty error to prevent saving
        }
        parent::afterValidate();
    }

    /**
     * Save models
     *
     * This method will save Order, Address and Items models
     * Make sure to call $this->validate() prior to this function.
     *
     * @return bool
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        Yii::debug($this->order);
        if (!$this->order->save()) {
            $transaction->rollBack();

            return false;
        }


        if ($this->address->save()) {
            $this->order->address_id = $this->address->id;
            if (!$this->order->save()) {

                $transaction->rollBack();
            }
        } else {
            $transaction->rollBack();

            return false;
        }

        if (!$this->saveItems()) {
            $transaction->rollBack();

            return false;
        }
        $transaction->commit();

        return true;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function saveItems()
    {
        $keep = [];
        foreach ($this->items as $item) {
            $item->order_id = $this->order->id;
            if (!$item->save(false)) {
                return false;
            }
            $keep[] = $item->id;
        }
        $query = Item::find()->andWhere(['order_id' => $this->order->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }
        foreach ($query->all() as $item) {
            $item->delete();
        }

        return true;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param $order
     */
    public function setOrder($order)
    {
        if ($order instanceof Order) {
            $this->_order = $order;
        } else {
            if (is_array($order)) {
                $this->_order->setAttributes($order);
            }
        }
    }

    /**
     * @return Address
     */
    protected function getAddress()
    {
        if ($this->_address === null) {
            $this->_address = $this->order->isNewRecord ? new Address() : $this->order->address;
        }

        return $this->_address;
    }

    /**
     * @param $address
     */
    public function setAddress($address)
    {
        if ($address instanceof Address) {
            $this->_address = $address;
        } else {
            if (is_array($address)) {
                if ($this->_address === null) {
                    $this->_address = $this->getAddress();
                }
                $this->_address->setAttributes($address);
            }
        }
    }

    /**
     * @return array|Item[]|mixed
     */
    public function getItems()
    {
        if ($this->_items === null) {
            $this->_items = $this->order->isNewRecord ? [] : $this->order->items;
        }

        return $this->_items;
    }

    /**
     * @param $key
     *
     * @return bool|Item|null|static
     */
    protected function getItem($key)
    {
        $item = $key && strpos($key, 'new') === false ? Item::findOne($key) : false;
        if (!$item) {
            $item = new Item();
            $item->loadDefaultValues();
        }

        return $item;
    }

    /**
     * @param $items
     */
    public function setItems($items)
    {
        unset($items['__id__']); // remove the hidden "new Item" row
        $this->_items = [];
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $this->_items[$key] = $this->getItem($key);
                $this->_items[$key]->setAttributes($item);
            } else {
                if ($item instanceof Item) {
                    $this->_items[$item->id] = $item;
                }
            }
        }
    }

    /**
     * @param $form
     *
     * @return string
     */
    public function errorSummary($form)
    {
        $errorLists = [];
        foreach ($this->getAllModels() as $id => $model) {
            $errorList    = $form->errorSummary($model, [
                'header' => '<p>Please fix the following errors for <b>' . $id . '</b></p>',
                'class'  => 'alert alert-danger',
            ]);
            $errorList    = str_replace('<li></li>', '', $errorList); // remove the empty error
            $errorLists[] = $errorList;
        }

        return implode('', $errorLists);
    }

    /**
     * @return array
     */
    protected function getAllModels()
    {
        $models = [
            'Order'   => $this->order,
            'Address' => $this->address,
        ];
        foreach ($this->items as $id => $item) {
            $models['Item.' . $id] = $this->items[$id];
        }

        return $models;
    }
}