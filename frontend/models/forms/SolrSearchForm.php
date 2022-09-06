<?php

namespace frontend\models\forms;

use yii\base\Model;

class SolrSearchForm extends Model
{
    public $query;

    public function rules() {
        return [
            [['query'], 'string']
        ];
    }
}
