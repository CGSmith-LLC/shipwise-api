<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApiConsumer */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'API Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="api-consumer-secret">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'label',
            [
                'attribute' => 'auth_key',
                'label' => 'API Key'
            ],
            [
                'attribute' => 'encrypted_secret',
                'label' => 'API Secret',
                'value' => function($model) {
                    return Yii::$app->getSecurity()->decryptByKey(base64_decode($model->encrypted_secret), Yii::$app->params['encryptionKey']);
                }
            ],
        ],
    ]) ?>

</div>
