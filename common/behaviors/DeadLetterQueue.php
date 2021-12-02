<?php

namespace common\behaviors;

use common\models\QueueDead;
use yii\queue\ExecEvent;
use yii\queue\Queue;

class DeadLetterQueue extends \yii\base\Behavior
{

    public function events()
    {
        return array_merge(parent::events(), [
            Queue::EVENT_AFTER_ERROR => 'addToDeadLetterQueue',
        ]);
    }

    public function addToDeadLetterQueue(ExecEvent $event)
    {
        if (!$event->retry) {
            $model = new QueueDead();
            $model->error = $event->error->getMessage();
            $model->job = $event->job;
            $model->save();
        }
    }
}