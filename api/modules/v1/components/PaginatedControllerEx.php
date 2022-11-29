<?php

namespace api\modules\v1\components;

use yii\helpers\ArrayHelper;

/**
 * Class PaginatedControllerEx
 *
 * @package api\modules\v1\components
 */
class PaginatedControllerEx extends ControllerEx
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), $this->addPagination());
    }

    public function addPagination()
    {
        return [
            'Pagination' => [
                'class' => 'api\modules\v1\components\parameters\Pagination',
            ],
            'Search'     => 'api\modules\v1\components\parameters\Search',
            'Limit'      => 'api\modules\v1\components\parameters\Limit',
        ];
    }
}