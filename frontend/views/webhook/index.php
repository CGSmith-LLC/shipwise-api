<?php

use common\models\Status;
use common\models\Webhook;
use common\models\WebhookTrigger;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Webhooks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webhook-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Webhook', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    Yii::debug(
             Webhook::find()
                ->joinWith([
                    'webhookTrigger'
                ])
                ->where([
                    Webhook::tableName() . '.customer_id' => 1,
                    Webhook::tableName() . '.active' => Webhook::STATUS_ACTIVE,
                    WebhookTrigger::tableName() . '.status_id' => 2,
                ])
                ->all()
    );

    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'endpoint',
            'authentication_type',
            'user',
            'pass',
            //'customer_id',
            //'when',
            //'active',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
