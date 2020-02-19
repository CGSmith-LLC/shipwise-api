<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\BulkAction */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Bulk Actions"];
YiiAsset::register($this);
?>
    <div class="bulk-action-view">

        <?php Pjax::begin([
            'id' => 'pjax-container',
        ]); ?>

        <div class="jumbotron">
            <p class="lead"><?= $model->name ?></p>
            <?php
            $btnClass = $model->isCompleted() ? 'success' : 'default';
            $disabled = $model->isCompleted() ? '' : 'disabled';
            ?>
            <div><?= Html::a('Print', false,
                    ['class' => "btn btn-lg btn-$btnClass $disabled", 'style' => 'min-width:170px;']) ?></div>
        </div>

        <h4>
            Bulk action status is: <span class="label label-<?= $model->statusColor ?>"><?= $model->statusName ?></span>

            <?php if ($model->isProcessing()) :
                $sinceStart = $model->processingSince(); ?>
                <span class="small"> since <?= $sinceStart->i ?> min <?= $sinceStart->s ?> sec</span>
            <?php endif; ?>
        </h4>
        <hr/>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query'      => $model->getItems(),
                'sort'       => false,
                'pagination' => false,
            ]),
            'tableOptions' => ['class' => 'table table-striped table-hover'],
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                //'order_id',
                'order.customer_reference',
                //'order.address.name',
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return '<span class="label label-' . $model->statusColor . '"> ' . $model->statusName . '</span>';
                    },
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>

    </div>

<?php
$this->registerJs("

    // @todo stop after bulk action status is completed!!!
    
    var refreshIntervalId = setInterval(function(){ 
        $.pjax.reload({container: '#pjax-container', async: false}); 
    }, 4000);
    
    ", View::POS_READY,
    'bulk-result-handler'
);
