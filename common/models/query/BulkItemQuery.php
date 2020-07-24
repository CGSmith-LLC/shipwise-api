<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\base\BaseBulkItem]].
 *
 * @see \common\models\base\BaseBulkItem
 */
class BulkItemQuery extends \yii\db\ActiveQuery
{

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\base\BaseBulkItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\base\BaseBulkItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
