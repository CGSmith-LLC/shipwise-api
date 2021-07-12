<?php

namespace console\controllers;

use common\models\BulkAction;
use common\models\Order;
use common\models\Status;
use console\jobs\orders\DownloadTrackingJob;
use console\jobs\orders\ParseOrderJob;
use yii\console\{Controller, ExitCode};

// To create/edit crontab file: crontab -e
// To list: crontab -l
// // m h  dom mon dow   command
// */5 * * * * /var/www/html/yii cron/frequent
// */15 * * * * /var/www/html/yii cron/quarter
// */30 * * * * /var/www/html/yii cron/halfhourly
// 0 * * * * /var/www/html/yii cron/hourly
// 15 1 * * * /var/www/html/yii cron/overnight
// 15 3 * * 5 /var/www/html/yii cron/weekly

/**
 * Class CronController
 *
 * @package console\controllers
 */
class CronController extends Controller
{

    /**
     * @var boolean whether to run the command interactively.
     */
    public $interactive = false;

    /**
     * Action Index
     * @return int exit code
     */
    public function actionIndex()
    {
        $this->stdout('Yes, service cron is running');
        return ExitCode::OK;
    }

    /**
     * Action Frequent
     * Called every five minutes
     *
     * @return int exit code
     */
    public function actionFrequent()
    {
        // ...
        /**
         * 1. Loop through customers and customer meta data to find ecommerce site
         * 2. query ecommerce site for new orders
         * 3. save the orders and create 'ParseOrders' job.
         */

        if (date('i') % 10 === 0)
        {
            /**
             *  foreach customer:
             *      foreach integration:
             *          - Get integration metadata
             *          - call parseOrder
             */

        }

        return ExitCode::OK;
    }

    /**
     * Action Quarter
     * Called every fifteen minutes
     *
     * @return int exit code
     */
    public function actionQuarter()
    {
        //

        return ExitCode::OK;
    }

    /**
     * Action Half Hourly
     * Called every 30 minutes
     *
     * @return int exit code
     */
    public function actionHalfhourly()
    {
        //

        return ExitCode::OK;
    }

    /**
     * Action Hourly
     * @return int exit code
     */
    public function actionHourly()
    {
        /**
         * every hour
         */
        $currentHour = date('G');

        /**
         * every four hours
         */
        if (($currentHour % 4) == 0) {
            //
        }

        /**
         * every six hours
         */
        if (($currentHour % 6) == 0) {
            //
        }

        $this->queuePendingOrders();

        return ExitCode::OK;
    }

    /**
     * Action Overnight
     * Called every night
     *
     * @return int exit code
     */
    public function actionOvernight()
    {
        $this->cleanBulkActionData();

        return ExitCode::OK;
    }

    private function queuePendingOrders()
    {
        $pendingOrders = Order::find()->where(['status_id' => Status::PENDING])->all();

        foreach ($pendingOrders as $pendingOrder) {
            \Yii::$app->queue->push(new DownloadTrackingJob(['orderId' => $pendingOrder->id]));
        }
    }

    /**
     * Clean `bulk_action` and `bulk_item` database tables for data older than 24 hours.
     */
    private function cleanBulkActionData()
    {
        $bulkActions = BulkAction::find()->olderThan(24)->all();

        foreach ($bulkActions as $bulkAction) {
            $bulkAction->delete(); // will trigger deletion of its related models
        }
    }

}
