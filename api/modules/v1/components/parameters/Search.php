<?php

namespace api\modules\v1\components\parameters;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class Search extends Behavior
{

    public $search;

    /**
     * @inheritdoc
     * @return array
     */
    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            Controller::EVENT_BEFORE_ACTION => 'getSearch',
        ]);
    }

    /**
     * @param $event
     */
    public function getSearch($event)
    {
        $request      = \Yii::$app->request;
        $this->search = $request->get("search");
    }
}