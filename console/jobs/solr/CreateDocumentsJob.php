<?php

namespace console\jobs\solr;

use common\components\SolrService;
use common\models\Order;

class CreateDocumentsJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    public $orderIds;

   public function execute($queue)
    {
        /** @var SolrService $solr */
        $solr = \Yii::$app->solr;
        $orders = Order::find()->where(['id' => $this->orderIds])->all();
        $solr->createDocument($orders);
    }


    public function getTtr()
    {
        return 300; // seconds
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 3);
    }
}