<?php


/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 * @var frontend\models\search\CustomerSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\bootstrap\Alert;
use yii\grid\GridView;
use yii\helpers\Html;

$userId = $user->id;
?>

<?php $this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>

<?= Alert::widget([
    'options' => [
        'class' => 'alert-info alert-dismissible',
    ],
    'body' => Yii::t('user', 'You can link multiple customers by using the list below'),
]) ?>

<h3>Customers</h3>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'name',
        'city',
        //'created_date',
        [
            'header' => Yii::t('user', 'Association'),
            'value' => function ($model) use ($userId) {
                if ($model->isLinkedToUser($userId)) {
                    return Html::a(Yii::t('user', 'Unlink'),
                        ['link-customer', 'id' => $userId, 'cid' => $model->id],
                        [
                            'class' => 'btn btn-xs btn-danger btn-block',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('user',
                                'Are you sure you want to unlink user from this customer?'),
                        ]);
                } else {
                    return Html::a(Yii::t('user', 'Link'),
                        ['link-customer', 'id' => $userId, 'cid' => $model->id],
                        [
                            'class' => 'btn btn-xs btn-success btn-block',
                            'data-method' => 'post',
                            //'data-confirm' => Yii::t('user', 'Are you sure you want to link user to this customer?'),
                        ]);
                }
            },
            'format' => 'raw',
        ],
    ],
]); ?>

<?php $this->endContent() ?>
