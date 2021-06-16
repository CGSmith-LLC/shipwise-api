<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\BulkEditForm */
/* @var $result array Orders to show */
/* @var $status string */

$this->title = 'Bulk Order Edit Confirm';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-bulk-edit-confirm',
        'enableClientValidation' => false,
    ]); ?>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <p>You are about to change <em>all</em> of the orders below to a status of <strong><?= $status ?></strong>. Proceed?</p>
        <div class="form-group">
            <?= Html::submitButton('Confirm', ['class' => 'btn btn-lg btn-success']) ?>
        </div>
    </div>
    <div class="col-md-12">
        <ul>
        <?php
        //var_dump($result);
            foreach ($result as $order) {
                echo '<li>' . $order['customer_reference'] . '</li>';
            }
        ?>
        </ul>
    </div>
</div>


<?php ActiveForm::end(); ?>

