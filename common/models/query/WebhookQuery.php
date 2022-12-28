<?php

namespace common\models\query;

class WebhookQuery extends BaseQuery
{

    /**
     * Ensures customer_id is maintained for users that require restriction on the model
     *
     * @todo this should move to the basequery then all models should reference
     *
     * @param $modelClass
     * @param $config
     */
    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);

        // Admins can see all models - so admins skip this andWhere()
        if (!\Yii::$app->user->identity->isAdmin) {
            $this->andWhere(['in', 'customer_id', \Yii::$app->user->identity->customerIds]);
        }
    }
}