<?php

namespace common\traits;

use yii\helpers\Json;

/**
 * Trait MetaDataFieldTrait
 * @package common\traits
 */
trait MetaDataFieldTrait
{
    public array $array_meta_data = [];

    public function isMetaKeyExistsAndNotEmpty(string $key): bool
    {
        return (isset($this->array_meta_data[$key]) && !empty($this->array_meta_data[$key]));
    }

    protected function convertMetaData(): void
    {
        if ($this->meta) {
            $this->array_meta_data = Json::decode($this->meta);
        }
    }
}
