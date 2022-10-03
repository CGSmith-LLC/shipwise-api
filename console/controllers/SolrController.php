<?php

namespace console\controllers;

use console\jobs\solr\CreateDocumentsJob;
use Symfony;
use frontend\models\Order;
use yii\console\Controller;


class SolrController extends Controller
{
    /**
     * Add all orders to the solr index
     */
    public function actionCreateIndex()
    {
        ini_set('memory_limit', '-1');
        $query = Order::find()->select(['id']);
        foreach ($query->batch(2000) as $orders) {
            $orderIds = array_map(function($order) {return $order->id;}, $orders);
            \Yii::$app->queue->push(new CreateDocumentsJob([
              'orderIds' => $orderIds,
          ]));
        }
    }
}
