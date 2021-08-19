<?php

namespace frontend\models\forms;

use common\models\Integration;
use common\models\IntegrationMeta;
use yii\base\Model;

class BaseForm extends Model
{

    /** {@inheritdoc} */
    public function afterValidate()
    {
        if (!Model::validateMultiple($this->getAllModels())) {
            $this->addError(null); // add an empty error to prevent saving
        }
        parent::afterValidate();
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
            $errorList = $form->errorSummary($model, [
                'header' => '<p>Please fix the following errors for <b>' . $id . '</b></p>',
                'class' => 'alert alert-danger',
            ]);
            \Yii::debug($errorList);
            $errorList = str_replace('<li></li>', '', $errorList); // remove the empty error
            $errorLists[] = $errorList;
        }
        return implode('', $errorLists);
    }

}