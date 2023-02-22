<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\EcommercePlatform;
use common\models\search\EcommercePlatformSearch;

/* @var $this View */
/* @var $searchModel EcommercePlatformSearch */
/* @var $dataProvider ActiveDataProvider */

$title = 'Ecommerce Platforms';
$this->title = $title . ' - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = $title;
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
        <?php if (Yii::$app->request->getQueryParam('EcommercePlatformSearch')) { ?>
            <a href="<?= Url::to(['/ecommerce-platform/index']) ?>" class="btn btn-default">Clear filters</a>
        <?php } ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items}',
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    $string = Html::encode($model->name);
                    $string .= '<br><small class="text-muted">Connected customers: ' . $model->getConnectedCustomersCounter() . '</small>';

                    if ($model->updated_date) {
                        $string .= '<br><small class="text-muted">Last update: ' . Yii::$app->formatter->asDatetime($model->updated_date) . '</small>';
                    }

                    return $string;
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    EcommercePlatform::getStatuses(),
                    ['class' => 'form-control', 'prompt' => 'All Statuses']
                ),
                'value' => function($model) {
                    if ($model->status == EcommercePlatform::STATUS_PLATFORM_ACTIVE) {
                        $string = '<span class="text-success" title="Active"><i class="glyphicon glyphicon-ok-circle"></i> Active</span>';
                    } else {
                        $string = '<span class="text-danger" title="Inactive"><i class="glyphicon glyphicon-ban-circle"></i> Inactive</span>';
                    }

                    return $string;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'text-center'],
                'header' => 'Actions',
                'template' => '<div class="text-center">{view} {update} {status}</div>',
                'buttons' => [
                    'status' => function ($url, $model, $key) {
                        $url = Url::to(['/ecommerce-platform/status', 'id' => $model->id]);
                        $icon = ($model->isActive())
                            ? '<i class="glyphicon glyphicon-ban-circle" title="Make inactive"></i>'
                            : '<i class="glyphicon glyphicon-ok-circle" title="Make active"></i>';
                        $confirm = ($model->isActive())
                            ? 'Are you sure you want to make this platform inactive? In this case, all integrations with the platform will be paused.'
                            : 'Are you sure you want to make this platform active?';

                        return '<a href="' . $url . '" onclick="return confirm(\'' . $confirm . '\')">' . $icon . '</a>';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
