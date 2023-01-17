<?php

use frontend\assets\DatePickerAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\forms\ReportForm */
/* @var $customers array of customers */

DatePickerAsset::register($this);
$this->registerJs('
            // Datepicker
            $(\'.date\').datepicker({
                todayBtn           : \'linked\',
                keyboardNavigation : false,
                forceParse         : false,
                autoclose          : true,
                format             : \'mm/dd/yyyy\',
                todayHighlight     : true,
            });');
$this->title = Yii::$app->name;
?>
<div class="report-index">

    <div class="body-content">
        <h1>Reports</h1>

        <div class="report-form">

            <?= \yii\bootstrap\Tabs::widget([
            'items' => [
                [
                    'label' => 'By Date',
                    'content' => $this->render('_tabDate', [
                        'model' => $model,
                        'customers' => $customers,
                    ]),
                    'active' => $model->scenario === \frontend\models\forms\ReportForm::SCENARIO_BY_DATE,
                ],
                [
                    'label' => 'By Order #',
                    'content' => $this->render('_tabOrderNr', [
                        'model' => $model,
                    ]),
                    'active' => $model->scenario === \frontend\models\forms\ReportForm::SCENARIO_BY_ORDER_NR,
                ],

            ],
        ])?>
        </div>
    </div>
</div>
