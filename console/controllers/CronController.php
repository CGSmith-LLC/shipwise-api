<?php

namespace console\controllers;

use common\models\BulkAction;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;
use common\models\EcommerceWebhook;
use common\models\FulfillmentMeta;
use common\models\Order;
use common\models\ScheduledOrder;
use common\services\platforms\ShopifyService;
use console\jobs\orders\FetchJob;
use console\jobs\orders\SendTo3PLJob;
use yii\console\{Controller, ExitCode};
use common\models\Integration;
use yii\base\InvalidConfigException;
use yii\db\Exception;

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
    public function actionIndex(): int
    {
        $this->stdout('Yes, service cron is running');
        return ExitCode::OK;
    }

    /**
     * Action Frequent
     * Called every five minutes
     * @return int exit code
     * @throws InvalidConfigException
     */
    public function actionFrequent(): int
    {
        /**
         * 1. Loop through customers and customer meta data to find ecommerce site
         * 2. query ecommerce site for new orders
         * 3. save the orders and create 'ParseOrders' job.
         */

        /**
         *  foreach customer:
         *      foreach integration:
         *          - Get integration metadata
         *          - call parseOrder
         */
        $this->runIntegrations(Integration::ACTIVE);
        $this->runScheduledOrders();

        $this->runEcommerceIntegrations();
        $this->runEcommerceWebhooks();

        return ExitCode::OK;
    }

    /**
     * This method is used for pulling first (initial) raw orders from E-commerce platforms like Shopify.
     * For working with webhooks, the method `runEcommerceWebhooks()` is used.
     * @throws InvalidConfigException
     */
    protected function runEcommerceIntegrations(): void
    {
        $ecommerceIntegrations = EcommerceIntegration::find()
            ->active()
            ->orderById()
            ->limit(100)
            ->all();

        foreach ($ecommerceIntegrations as $ecommerceIntegration) {
            switch ($ecommerceIntegration->ecommercePlatform->name) {

                /**
                 * Shopify:
                 */
                case EcommercePlatform::SHOPIFY_PLATFORM_NAME:

                    $accessToken = $ecommerceIntegration->array_meta_data['access_token'];

                    if ($accessToken) {
                        $shopifyService = new ShopifyService($ecommerceIntegration->array_meta_data['shop_url'], $ecommerceIntegration);
                        $orders = $shopifyService->getOrdersList();

                        foreach ($orders as $order) {
                            $shopifyService->parseRawOrderJob($order);
                        }
                    }

                    break;

            }
        }
    }

    /**
     * This method is used for creating Jobs for webhooks with the status "received".
     */
    protected function runEcommerceWebhooks(): void
    {
        $ecommerceWebhooks = EcommerceWebhook::find()
            ->received()
            ->orderById()
            ->limit(100)
            ->all();

        /**
         * @var $ecommerceWebhook EcommerceWebhook
         */
        foreach ($ecommerceWebhooks as $ecommerceWebhook) {
            $ecommerceWebhook->createJob();
        }
    }

    public function runIntegrations($status)
    {
        /** @var Integration $integration */
        foreach (Integration::find()->where(['status' => $status])->andWhere(['webhooks_enabled' => 0])->with('meta')->all() as $integration) {
            \Yii::$app->queue->push(new FetchJob([
                'integration' => $integration
            ]));
        }
    }

    public function runScheduledOrders()
    {
        $date = new \DateTime();
        foreach (ScheduledOrder::find()->where(['<=', 'scheduled_date', $date->format('Y-m-d')])->all() as $scheduledOrder) {
            $order = Order::findOne($scheduledOrder->order_id);
            $order->status_id = $scheduledOrder->status_id;
            if ($order->save()) {
                $scheduledOrder->delete();
            }
        }
    }

    public function actionTest()
    {

        /*
        $newInt = new Fulfillment([
            'name' => "Coldco",
        ]);

        $newInt->save();//*/

        //FulfillmentMeta::addMeta('access_token', '', 1);
        //IntegrationMeta::addMeta('api_key', '4d3f8cfe2fe56cffd14beca0ca583cd2',2);
        //IntegrationMeta::addMeta('api_secret', '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',2);

        return ExitCode::OK;
    }

    public function actionCreateFulfillmentMeta($key, $val, $fulfillment_id)
	{
		try {
			FulfillmentMeta::addMeta(key: $key, value: $val, id: $fulfillment_id);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function actionCreateSendTo3PLJob($order, $fulfillment)
	{
		try {
			\Yii::$app->queue->push(new SendTo3PLJob(['order_id' => $order, 'fulfillment_name' => $fulfillment]));
		} catch (Exception $e) {
			echo $e->getMessage();
		}
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
        $this->runIntegrations(Integration::ERROR);

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
