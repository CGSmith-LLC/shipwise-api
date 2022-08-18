<?php

namespace frontend\models\forms;

use Da\User\Form\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{

    public function rules()
    {
        $rules = parent::rules();
        unset($rules['usernameRequired']);
        return $rules;
    }
}