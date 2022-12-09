<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\CsvBox;
use common\adapters\ecommerce\DudaAdapter;
use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderCancelledException;
use common\models\IntegrationHookdeck;
use common\models\UnparsedProductEvent;
use console\jobs\NotificationJob;
use console\jobs\orders\ParseOrderJob;
use frostealth\yii2\aws\s3\Service;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UnauthorizedHttpException;

/**
 * Class WebhookController
 *
 * @package api\modules\v1\controllers
 *
 */
class WebhookController extends ControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST'],
            'urban-smokehouse' => ['POST'],
            'import' => ['POST'],
        ];
    }

    /**
     * A way to validate the Hookdeck signature
     *
     * @param Request $request
     * @return void
     * @throws \Exception
     */
    public function isValid(Request $request): void
    {
        if ('' === $request->headers->get('x-hookdeck-signature')) {
            throw new \Exception('Unauthorized', 403);
        }

        $hmacHeader = $request->headers->get('x-hookdeck-signature');
        $hash = base64_encode(
            hash_hmac(
                'sha256',
                $request->getRawBody(),
                Yii::$app->params['hookdeckSigningSecret'],
                true
            )
        );

        if (!hash_equals($hmacHeader, $hash)) {
            throw new \Exception('Unauthorized', 403);
        }
    }

    public function actionImport()
    {
        try {
            $csvBox = new CsvBox(Yii::$app->request->getBodyParams());
            // setting file_path as attribute makes it easier in the model
            $csvBox->file_path = Yii::$app->params['csvBoxS3Path'];
            Yii::debug($csvBox->getS3FilePath());
            $this->isValid(Yii::$app->request);

            // get file
            /** @var Service $s3 */
            $s3 = Yii::$app->get('csvboxstorage');
            $csvBox->file_stream = $s3->get($csvBox->getS3FilePath());

            // if import failed send email to user
            $csvBox->import();
            if (!empty($csvBox->getErrorSummary(true))) {
                $errors = "<li>" . implode("</li><li>", $csvBox->getErrorSummary(true)) . "</li>";
                \Yii::$app->queue->push(
                    new NotificationJob([
                        'customer_id' => $csvBox->customer_id,
                        'subject' => '⚠️ Problem importing order file ' . $csvBox->original_filename,
                        'message' => 'This file failed to import for the following reasons: <ul>' . $errors . '</ul>',
                        'url' => ['/order/import',],
                        'urlText' => 'Reupload and Import File',
                    ])
                );
                return $this->errorMessage('File could not be imported');
            }

            return $this->success('Imported successfully');
        } catch (\Exception $e) {
            return $this->errorMessage(500, $e->getMessage());
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $headers = Yii::$app->request->headers;

        /**
         * 1. Validate the hookdeck source. If there is no source we throw a 404
         * 2. Check source against DB to see if hookdeck exists
         * 3. @todo validate the hookdeck HMAC and webhook HMAC
         * 4. @todo log unparsed order into DB for future parsing
         * 5. @todo throw 500 if queue cannot be pushed too
         */
        if ($sourceName = $headers->get('X-Hookdeck-Source-Name', false)) {
            /** @var IntegrationHookdeck $integrationHookdeck */
            if ($integrationHookdeck = IntegrationHookdeck::find()->where(['source_name' => $sourceName])->one()) {
                $id = \Yii::$app->queue->push(
                    new ParseOrderJob([
                        'unparsedOrder' => Yii::$app->request->bodyParams,
                        'integration_id' => $integrationHookdeck->integration_id,
                    ])
                );
                return $this->success('Queued ' . $id);
            } else {
                return $this->errorMessage(404, 'Unknown source name');
            }
        } else {
            return $this->errorMessage(404, 'Missing source name');
        }
    }

    public function actionUrbanSmokehouse()
    {
        $headers = Yii::$app->request->headers;
        $customer_id = 76;
        if ($headers->get('authorization') === 'Basic c2hpcHdpc2U6bmVlc3ZpZ3M=') {
            $duda = new DudaAdapter();
            /**
             * @TODO Replacable by the behaviors with meta info - this is temporary
             */
            $duda->on(DudaAdapter::EVENT_BEFORE_ITEM_PARSE, function (UnparsedProductEvent $event) {
                switch ($event->unparsedItem['selectedOptions'][0]['value']) {
                    case '4 Half Slabs':
                        $multiplier = 4;
                        break;
                    case '6 Half Slabs':
                        $multiplier = 6;
                        break;
                    case '8 Half Slabs':
                        $multiplier = 8;
                        break;
                    case '10 Half Slabs':
                        $multiplier = 10;
                        break;
                    case '12 Half Slabs':
                        $multiplier = 12;
                        break;
                    default:
                        $multiplier = 1;
                }
                $event->unparsedItem['quantity'] = $event->unparsedItem['quantity'] * $multiplier;
            });
            $duda->customer_id = $customer_id; // urban smokehouse
            try {
                $order = $duda->parseOrder(Yii::$app->request->getRawBody());
                $order->save();
            } catch (OrderCancelledException $exception) {
                return $this->errorMessage(200, 'Order sent to cancel job successfully');
            } catch (IgnoredWebhookException $exception) {
                return $this->errorMessage(200, $exception->getMessage());
            } catch (\Exception $exception) {
                return $this->errorMessage(500, $exception->getMessage());
            }
        } else {
            throw new  UnauthorizedHttpException();
        }
    }

    public function behaviors()
    {
        return Controller::behaviors();
    }
}