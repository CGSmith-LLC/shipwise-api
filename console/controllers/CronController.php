<?php

namespace console\controllers;

use common\models\BulkAction;
use common\models\IntegrationMeta;
use common\services\ShopifyService;
use console\jobs\orders\ParseOrderJob;
use yii\console\{Controller, ExitCode};
use common\models\Integration;
use PHPUnit\Util\ExcludeList;
use SebastianBergmann\CodeCoverage\Report\PHP;

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

        //if (date('i') % 10 === 0) { TODO: Uncomment If
            /**
             *  foreach customer:
             *      foreach integration:
             *          - Get integration metadata
             *          - call parseOrder
             */

            echo 'processing integrations...' . PHP_EOL;
            /** @var Integration $integration */
            foreach (Integration::find()->all() as $integration) {

                $orders = $integration->getInterface()->getOrders();

                echo "\tpushing orders..." . PHP_EOL;
                foreach ($orders as $order) {
                    echo "\t\tpushing order...\t";
                    \Yii::$app->queue->push(new ParseOrderJob([
                        "order" => $order,
                        "ecommerceSite" => $integration->name,
                        "customer_id" => $integration->customer_id,
                    ]));
                    echo "\t\tpushed order" . PHP_EOL;
                }
                echo "\tpushed orders" . PHP_EOL;
            }
            echo 'processed integrations' . PHP_EOL . PHP_EOL;
    //    }

        return ExitCode::OK;
    }

    public function actionTest()
    {
        /*IntegrationMeta::addMeta(ShopifyService::META_URL, 'https://hu-kitchen-2.myshopify.com/', 1);//*/
        /*IntegrationMeta::addMeta(ShopifyService::META_API_KEY, '537fb667e32cadb69c8a42c47ed8e97c', 1);//*/
        /*IntegrationMeta::addMeta(ShopifyService::META_API_SECRET, '7b3a88dc57b69822a82bbc8956c3fac5', 1);//*/
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
