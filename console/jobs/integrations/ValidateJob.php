<?php


namespace console\jobs\integrations;


use common\models\Integration;
use yii\base\BaseObject;
use yii\console\Exception;
use yii\db\Expression;
use yii\queue\JobInterface;

class ValidateJob extends BaseObject implements JobInterface
{

    /** @var Integration $integration */
    public $integration;

    public function execute($queue)
    {
        // try to connect to the integration and update status to active if possible to connect

        // get adapter for integration
        $service = $this->integration->getService();

        try {
            $service->testConnection();
            // @todo convert all times to UTC 0 then translate
            // @todo store last order #?
            $expression = new Expression('NOW()');
            $this->integration->status = Integration::ACTIVE;
            $this->integration->status_message = "Integration connected.";
            $this->integration->last_success_run = $expression;
            $this->integration->save();
        } catch (Exception $exception) {
            $this->integration->status = Integration::ERROR;
            $this->integration->status_message = $exception->getMessage();
            $this->integration->save();
        }
    }

}