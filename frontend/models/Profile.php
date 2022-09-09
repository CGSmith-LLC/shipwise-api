<?php

namespace frontend\models;


class Profile extends \Da\User\Model\Profile
{
    public $cloneOrderPreference;

    public function rules()
    {
        $rules = parent::rules();
        $rules['cloneOrderPreferenceRequired'] = ['clone_order_preference', 'required'];
        return $rules;
    }
}