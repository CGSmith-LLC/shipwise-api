<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \frontend\models\forms\ReportForm */
/* @var $customers array of customers */

?>
<div class="speedee-manifest">
    <div class="body-content">
        <h1>Generate SpeeDee Delivery Manifest</h1>
        <p>
            The following orders are pending for SpeeDee Delivery. Click the button below to generate a shipment manifest
            and forward it to SpeeDee Delivery for processing.
        </p>
        <div class="generate-manifest-button">
            <?php $form = ActiveForm::begin(); ?>
                <?php echo $form->field($model, 'customer')
                    ->dropdownList($customers, [
                            'prompt' => ' Select customer...',
                    ]);
                ?>
                <?= Html::submitButton('Generate and Send', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end() ?>
            <table id="ordersTable" class="table">
                <thead>
                    <tr>
                        <th>Reference #</th>
                        <th>Recipient</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJsFile(
    '@web/js/speedee-ajax.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
