<?php

namespace common\behaviors;

use yii\base\{Behavior, Event};
use yii\db\ActiveRecord;
use yii\helpers\Json;

class JsonAttributeBehavior extends Behavior
{
    public bool $stripSlashes = true;
    public bool $stripTags = true;
    public string $arraySeparator = ',';

    public array $jsonAttributes = [];
    public array $convertFromJsonAttributes = [];

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'eventBeforeInsertAndUpdate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventBeforeInsertAndUpdate',
        ];
    }

    public function eventAfterFind(Event $event): void
    {
        foreach ($this->convertFromJsonAttributes as $k => $v) {
            $this->owner->{$k} = ($this->owner->{$v}) ? Json::decode($this->owner->{$v}) : [];
        }
    }

    public function eventBeforeInsertAndUpdate(Event $event): void
    {
        foreach ($this->jsonAttributes as $jsonAttribute) {
            if ($this->owner->{$jsonAttribute} && !preg_match("/\[(.*?)\]/si", $this->owner->{$jsonAttribute})) {
                if ($this->stripTags) {
                    $this->owner->{$jsonAttribute} = strip_tags($this->owner->{$jsonAttribute});
                }

                if ($this->stripSlashes) {
                    $this->owner->{$jsonAttribute} = stripslashes($this->owner->{$jsonAttribute});
                }

                $this->owner->{$jsonAttribute} = Json::encode(explode($this->arraySeparator, $this->owner->{$jsonAttribute}));
            }
        }
    }
}
