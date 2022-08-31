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
        $query = Order::find()->select(['id']);
        // 100 seems to be a sweet spot for getting the data pushed to the job
        foreach ($query->batch(100) as $orders) {
            \Yii::$app->queue->push(new CreateDocumentsJob([
              'orderIds' => $orders,
          ]));
        }
    }
}
