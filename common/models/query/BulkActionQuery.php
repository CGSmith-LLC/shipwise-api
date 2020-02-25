<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\base\BaseBulkAction]].
 *
 * @see \common\models\base\BaseBulkAction
 */
class BulkActionQuery extends \yii\db\ActiveQuery
{

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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

    /**
     * Where created_date is older than X hours
     *
     * @param int $hours
     *
     * @return $this
     */
    public function olderThan($hours)
    {
        return $this->andWhere(
            '(created_on < (NOW() - INTERVAL :hours HOUR))',
            [
                ':hours' => (int)$hours,
            ]
        );
    }
}
