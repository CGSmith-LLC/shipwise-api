<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Integration */
/* @var $metaModel \frontend\models\forms\integrations\WooCommerceForm */

$this->title = 'Add ' . $model->name . ' Integration Connection Details';
$this->params['breadcrumbs'][] = ['label' => 'Integrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="integration-form">
        <?php $form = ActiveForm::begin([
            'id' => 'integration-meta-form'
        ]); ?>

        <?php
        foreach ($metaModel->attributes() as $metaField) {
            Yii::debug($metaField);
            if ($metaField != 'type') {
                echo $form->field($metaModel, $metaField)->input('text');
            }
        }
        ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-lg btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


</div>
