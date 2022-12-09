<?php

use common\models\Status;
use common\models\Webhook;
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
                'webhookTrigger' => function (\yii\db\ActiveQuery $query) {
                    $query->andWhere(['webhook_trigger.status_id' => Status::CANCELLED]);
                }
            ])
            ->andWhere([
                'webhook.customer_id' => 1,
                'webhook.active' => Webhook::STATUS_ACTIVE,
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
