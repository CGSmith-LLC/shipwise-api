<?php

namespace common\models\query;

use common\models\AliasParent;

/**
 * This is the ActiveQuery class for [[AliasParent]].
 *
 * @see AliasParent
 */
class AliasParentQuery extends BaseQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    /**
     * {@inheritdoc}
     * @return AliasParent[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AliasParent|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
