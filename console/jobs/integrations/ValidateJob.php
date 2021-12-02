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
    protected $integration;

    /** @var int $integration_id Integration ID to lookup */
    public int $integration_id;

    public function execute($queue)
    {
        $this->integration = Integration::find()->where(['id' => $this->integration_id])->with('meta')->one();
        // try to connect to the integration and update status to active if possible to connect
        // get adapter for integration
        $service = $this->integration->getService();

        try {
            if ($service->canCreateWebhooks()) {
                $service->createWebhooks();
            } else {
                $service->testConnection();
                // @todo convert all times to UTC 0 then translate
                // @todo store last order #?
            }
            $this->integration->status = Integration::ACTIVE;
            $this->integration->status_message = "Integration connected.";
            $this->integration->last_success_run = new Expression('NOW()');
            $this->integration->save();
        } catch (Exception $exception) {
            $this->integration->status = Integration::ERROR;
            $this->integration->status_message = $exception->getMessage();
            $this->integration->save();
        }
    }

}