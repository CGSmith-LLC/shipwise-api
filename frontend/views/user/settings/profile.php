<?php
use Da\User\Helper\TimezoneHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var \frontend\models\Profile $model
 * @var TimezoneHelper         $timezoneHelper
 */

$this->title = Yii::t('usuario', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
$timezoneHelper = $model->make(TimezoneHelper::class);

?>

<div class="clearfix"></div>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                            'labelOptions' => ['class' => 'col-lg-3 control-label'],
                        ],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnBlur' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'clone_order_preference')
                    ->dropDownList(\common\models\Status::getList());
                ?>

                <?= $form
                    ->field($model, 'timezone')
                    ->dropDownList(ArrayHelper::map($timezoneHelper->getAll(), 'timezone', 'name'));
                ?>
                <?= $form
                    ->field($model, 'gravatar_email')
                    ->hint(
                        Html::a(
                            Yii::t('usuario', 'Change your avatar at Gravatar.com'),
                            'http://gravatar.com',
                            ['target' => '_blank']
                        )
                    ) ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
                        <br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
