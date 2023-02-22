<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $model EcommercePlatform */

$title = $model->name;
$this->title = $title . ' - Ecommerce Platforms - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Platforms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
        <small>
            <a href="<?= Url::to(['/ecommerce-platform/update', 'id' => $model->id]) ?>">
                <i class="glyphicon glyphicon-pencil" title="Update"></i>
            </a>
        </small>
    </h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'status',
                'format' => 'raw',
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
                'label' => 'Connected Customers',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getConnectedCustomersCounter();
                },
            ],
            [
                'attribute' => 'meta',
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->meta)
                        ? '<pre>' . HtmlPurifier::process(nl2br($model->meta)) . '</pre>'
                        : null;
                },
            ],
            'created_date:datetime',
            'updated_date:datetime',
        ],
    ]) ?>
</div>
