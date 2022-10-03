<?php

use common\models\Status;
use frontend\models\{Customer, BulkAction};
use yii\helpers\{Html, Json, Url};
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\SolrSearchForm */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array List of order statuses */

$this->title = 'Orders (solr)';
$this->params['breadcrumbs'][] = $this->title;
