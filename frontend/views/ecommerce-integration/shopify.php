<?php
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\forms\platforms\ConnectShopifyStoreForm;

/* @var $this View */
/* @var $model ConnectShopifyStoreForm */

$title = 'Shopify Integration';
$this->title = $title . ' - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'E-commerce Integrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
    </h1>

    <div class="well well-sm">
        <div class="mb-2">
            Please input your Shopify shop name and its URL without http(s).
            Example: <span class="text-muted">myshop.myshopify.com</span>.
        </div>
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form
                        ->field($model, 'name')
                        ->textInput([
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('name') . '...',
                            'required' => true,
                            'autofocus' => true,
                            'maxlength' => true
                        ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form
                        ->field($model, 'url')
                        ->textInput([
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('url') . '...',
                            'required' => true,
                            'maxlength' => true
                        ]) ?>
                </div>
                <div class="col-sm-12">
                    <?= Html::submitButton('Connect', [
                        'class' => 'btn btn-success',
                    ]) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
