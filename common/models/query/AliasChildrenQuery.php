<?php

namespace common\models\query;

use common\models\AliasChildren;

/**
 * This is the ActiveQuery class for [[AliasChildren]].
 *
 * @see AliasChildren
 */
class AliasChildrenQuery extends \yii\db\ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return AliasChildren[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AliasChildren|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
