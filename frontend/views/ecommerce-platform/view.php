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
$this->title = $title . ' - E-commerce Platforms - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'E-commerce Platforms', 'url' => ['index']];
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
            [
                'label' => 'Platform:',
                'attribute' => 'name',
            ],
            [
                'label' => 'Status:',
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->status == EcommercePlatform::STATUS_PLATFORM_ACTIVE) {
                        $string = '<span>Active</span> <i class="glyphicon glyphicon-ok-circle text-success" title="Active"></i>';
                    } else {
                        $string = '<span>Inactive</span> <i class="glyphicon glyphicon-ban-circle text-danger" title="Inactive"></i>';
                    }

                    return $string;
                },
            ],
            [
                'label' => 'Connected users:',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getConnectedUsersCounter();
                },
            ],
            [
                'label' => 'Meta data:',
                'attribute' => 'meta',
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->meta)
                        ? '<pre>' . HtmlPurifier::process(nl2br($model->meta)) . '</pre>'
                        : null;
                },
            ],
            [
                'label' => 'Created date:',
                'attribute' => 'created_date',
                'format' => 'datetime',
            ],
            [
                'label' => 'Updated date:',
                'attribute' => 'updated_date',
                'format' => 'datetime',
            ],
        ],
    ]) ?>
</div>
