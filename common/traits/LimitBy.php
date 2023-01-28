<?php

namespace common\traits;

trait LimitBy
{
    public function limitByWarehouse($ids = [])
    {
        $this->andWhere(['in', 'warehouse_id', \Yii::$app->user->identity->warehouseIds]);
    }

    public function limitByCustomer($ids = [])
    {
        $this->andWhere(['in', 'customer_id', \Yii::$app->user->identity->customerIds]);
    }
}