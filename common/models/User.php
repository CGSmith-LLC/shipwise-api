<?php

namespace common\models;

use Da\User\Event\UserEvent;

class User extends \Da\User\Model\User
{

    public function init()
    {
        $this->on(UserEvent::EVENT_BEFORE_REGISTER, function () {
            $this->username = $this->email;
        });

        $this->on(UserEvent::EVENT_BEFORE_CREATE, function () {
            $this->username = $this->email;
        });

        parent::init();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['fieldRequired'] = ['customer_id', 'required'];
        unset($rules['usernameRequired']);
        return $rules;
    }

    /**
     * Return the customer id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }
}