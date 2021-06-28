<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\ApiConsumer]].
 *
 * @see \common\models\ApiConsumer
 */
class ApiConsumerQuery extends \yii\db\ActiveQuery
{

    public function active()
    {
        return $this->andWhere('status=1');
    }


    /**
     * {@inheritdoc}
     * @return \common\models\base\BaseBulkAction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\base\BaseBulkAction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
