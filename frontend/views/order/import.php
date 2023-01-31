<?php
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */
/* @var $carriers array */
/* @var $services array */
/* @var $states array */
/* @var $customers array */

$this->title = 'Import orders';

$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

if ($model->customer) {
    $this->title .= ' (' . ($customers[$model->customer] ?? '') . ')';
}
?>

    <div class="order-import">
        <h1>Order Import</h1>
        <?php
        if ($model->customer) { ?>
            <p>
                Selected customer: <strong><?= Html::encode($customers[$model->customer]) ?></strong>
            </p>
            <p>
                <?php
                    if ($showSelectCustomer) {
                        echo Html::a('Select a new customer', ['order/import']);
                    }
                ?>
            </p>
            <div class="row">
            <div class="col-md-3">
            <button class="btn btn-lg btn-success" data-csvbox disabled onclick="importer.openModal();"><i class="fa fa-upload"></i> Import</button>
            <script type="text/javascript" src="https://js.csvbox.io/script.js"></script>
            <script type="text/javascript">
                function callback(result, data) {
                    if (result) {
                        window.location.href = "import?success=true";
                    } else {
                        alert("There was some problem uploading the sheet");
                    }
                }

                let importer = new CSVBoxImporter("<?= Yii::$app->params['csvBoxImportKey'] ?>", {}, callback);
                importer.setUser({
                    user_id: "<?= Yii::$app->user->identity->email?>",
                    customer_id: "<?= $model->customer; ?>",
                });
            </script>
            </div>
            </div>
        <?php } else { ?>
            <p>Select a customer and then you be able to upload a CSV file. If you want you can download
                <a href="https://app.csvbox.io/sample-csv-file/<?= Yii::$app->params['csvBoxImportKey'] ?>" target="_blank">our template file</a>.
            </p>
            <div class="row">
                <div class="col-md-3">
                    <?= Html::beginForm(['import'], 'get', ['id' => 'form-customer']) ?>
                    <div class="form-group">
                        <label>Select a customer</label>
                        <?= Html::activeDropDownList(
                            $model,
                            'customer',
                            $customers,
                            [
                                'prompt' => ' Please select',
                                'class' => 'form-control',
                            ]
                        ) ?>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        <?php } ?>

        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Reference data</h3>
                        <p class="panel-subtitle">Use the data below to map your data properly for carrier or service selections</p>
                    </div>
                    <div class="panel-body text-muted">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Carrier service codes</strong></p>
                                <?php
                                foreach ($services as $code) {
                                    if (!in_array($code, ['to-be-defined', 'tbd'])) {
                                        echo "<code>$code</code><br />";
                                    }
                                } ?>
                                <hr class="visible-xs visible-sm"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(
    "
$('#orderimport-customer').change(function() { 
    $('#form-customer').submit(); 
});
",
    View::POS_READY,
    'order-import-pos-ready'
);
