<?php

namespace api\modules\v1\controllers;

use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\CsvBox;
use common\adapters\ecommerce\DudaAdapter;
use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderCancelledException;
use common\models\IntegrationHookdeck;
use console\jobs\NotificationJob;
use console\jobs\orders\ParseOrderJob;
use frostealth\yii2\aws\s3\Service;

/**
 * Class WebhookController
 *
 * @package api\modules\v1\controllers
 */
class WebhookController extends ControllerEx
{
    /** @inheritdoc */
    protected function verbs(): array
    {
        return [
            'index' => ['GET', 'POST'],
            'urban-smokehouse' => ['POST'],
            'import' => ['POST'],
        ];
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return Controller::behaviors();
    }

    /**
     * csvbox.io import action.
     *
     * @return array|string
     * @throws \Exception
     */
    public function actionImport(): array|string
    {
        $data = Yii::$app->request->getBodyParams();

        try {
            $csvBox = new CsvBox($data);
            $csvBox->file_path = Yii::$app->params['csvBoxS3Path'];
            // log in user that requested this for events

            $identity = User::findOne(['email' => $csvBox->user_id]);
            Yii::$app->user->login($identity);

            Yii::debug($csvBox->getS3FilePath());

            if (!$csvBox->hasErrors()) {
                // Get file from S3
                /** @var Service $s3 */
                $s3 = Yii::$app->get('csvboxstorage');
                $csvBox->file_stream = $s3->get($csvBox->getS3FilePath());

                $csvBox->import();

                // If import failed send email to user
                if ($csvBox->hasErrors()) {
                    $this->sendImportErrorsSummary($csvBox);

                    return $this->errorMessage(500, 'File could not be imported');
                }

                return $this->success('Imported successfully');
            } else {
                $this->sendImportErrorsSummary($csvBox);

                return $this->errorMessage(500, 'Invalid data');
            }
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
        $identity = User::findOne(1);
        Yii::$app->user->login($identity);
        if ($headers->get('authorization') === 'Basic c2hpcHdpc2U6bmVlc3ZpZ3M=') {
            $duda = new DudaAdapter();
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
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * Collects existing errors and creates a needed job to send them.
     * @param CsvBox $csvBox
     */
    protected function sendImportErrorsSummary(CsvBox $csvBox): void
    {
        $errors = "<li>" . implode("</li><li>", $csvBox->getErrorSummary(true)) . "</li>";

        \Yii::$app->queue->push(
            new NotificationJob([
                'customer_id' => $csvBox->customer_id,
                'user_id' => Yii::$app->user->identity->id,
                'subject' => '⚠️ Problem importing order file ' . $csvBox->original_filename,
                'message' => 'This file failed to import for the following reasons: <ul>' . $errors . '</ul>',
                'url' => ['/order/import',],
                'urlText' => 'Reupload and Import File',
            ])
        );
    }
}