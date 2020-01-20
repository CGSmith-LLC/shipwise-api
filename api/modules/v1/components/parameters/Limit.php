<?php

namespace api\modules\v1\components\parameters;

use yii\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\rest\Controller;

/**
 * Class Limit
 *
 * @package api\modules\v1\components\parameters
 */
class Limit extends Behavior
{

    /** @var integer */
    public $limit;

    /** @inheritdoc */
    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            Controller::EVENT_BEFORE_ACTION => "getLimit",
        ]);
    }

    /**
     * @param $event
     */
    public function getLimit($event)
    {
        $this->limit = \Yii::$app->request->get("limit");
    }
}
