<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use frontend\models\{Subscription, SubscriptionItems};

/**
 * Class SubscriptionForm
 *
 * This model is behind Subscription interface
 *
 * @property Subscription   $subscription
 * @property SubscriptionItems[]  $items
 *
 * @package frontend\models\forms
 */
class SubscriptionForm extends Model
{

    /** @var Subscription */
    protected $_subscription;

    /** @var SubscriptionItems[] */
    protected $_items;

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            // These names represent the getItems or getSubscription calls below
            [['Subscription', 'Items'], 'required'],
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
        if (!$this->subscription->save()) {
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
            $item->subscription_id = $this->subscription->id;
            if (!$item->save(false)) {
                return false;
            }
            $keep[] = $item->id;
        }

        /**
         * Delete the items that are not saved with a change
         */
        $query = SubscriptionItems::find()->andWhere(['subscription_id' => $this->subscription->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }
        foreach ($query->all() as $item) {
            $item->delete();
        }

        return true;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->_subscription;
    }

    /**
     * Set the subscription object or try assigning attributes if it is an array
     *
     * @param $subscription
     */
    public function setSubscription($subscription)
    {
        if ($subscription instanceof Subscription) {
            $this->_subscription = $subscription;
        } else {
            if (is_array($subscription)) {
                $this->_subscription->setAttributes($subscription);
            }
        }
    }

    /**
     * @return array|SubscriptionItems[]|mixed
     */
    public function getItems()
    {
        if ($this->_items === null) {
            // If it is a new record return an empty array. else, return the subscription's items
            $this->_items = $this->subscription->isNewRecord ? [] : $this->subscription->items;
        }

        return $this->_items;
    }

    /**
     * @param $key integer primary key of SubscriptionItem record
     *
     * @return SubscriptionItems
     */
    protected function getItem($key)
    {
        $item = $key && !str_contains((string) $key, 'new') ? SubscriptionItems::findOne($key) : false;
        if (!$item) {
            $item = new SubscriptionItems();
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
                // if instance then just set the object to the items array
                if ($item instanceof SubscriptionItems) {
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
            $errorList    = str_replace('<li></li>', '', (string) $errorList); // remove the empty error
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
            'Subscription'   => $this->subscription,
        ];
        foreach ($this->items as $id => $item) {
            $models['SubscriptionItems.' . $id] = $this->items[$id];
        }

        return $models;
    }
}