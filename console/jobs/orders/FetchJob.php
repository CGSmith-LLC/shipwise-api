<?php


namespace console\jobs\orders;


use common\models\Integration;
use yii\db\Exception;
use \yii\base\BaseObject;
use yii\db\Expression;
use yii\queue\JobInterface;

class FetchJob extends BaseObject implements JobInterface
{

    /**
     * @var Integration $integration
     */
    public Integration $integration;

    public int $page = 1;

    public int $perPage = 100;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute($queue)
    {
        $service = $this->integration->getService();
        $service->page = $this->page;
        $service->perPage = $this->perPage;
        $service->last_success_run = $this->integration->last_success_run;
        try {
            /** @var \yii\httpclient\Response $response */
            $response = $service->getOrders();

            if ($this->page <= $response->getHeaders()->get('x-wp-totalpages')) {
                \Yii::$app->queue->delay(7200)->push(new FetchJob([
                    'integration' => $this->integration,
                    'page' => $this->page + 1,
                    'perPage' => $this->perPage,
                ]));
            }

            foreach ($response->getData() as $data) {
                \Yii::$app->queue->delay(7200)->push(new ParseOrderJob([
                    'unparsedOrder' => $data,
                    'integration' => $this->integration
                ]));
            }
            die;



            // @todo convert all times to UTC 0 then translate
            // @todo store last order #?
            $expression = new Expression('NOW()');
            $this->integration->last_success_run = $expression;
            $this->integration->save();
        } catch (\yii\console\Exception $exception) {
            $this->integration->status = Integration::ERROR;
            $this->integration->status_message = $exception->getMessage();
            $this->integration->save();
        }
    }
}