<?php

namespace frontend\models;

use common\models\Order as BaseOrder;
use yii\helpers\ArrayHelper;

/**
 * Class Order
 *
 * @package frontend\models
 */
class Order extends BaseOrder
{

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id'          => 'Order ID',
            'status_id'   => 'Status',
            'status.name' => 'Status',
            'address'     => 'Ship To',
        ]);
    }

}