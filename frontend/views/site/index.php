<?php

use frontend\assets\DatePickerAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
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



$this->registerJsFile('js/dashboard-search.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJs('
 $("#searchButton").click(function () {
    dashboardSearch();
 });
');
?>

<script>
    fetch("/site/json")
    .then(response => response.json())
    .then(data => console.log(data))
    // JSON.parse(json);
    console.log(json);

</script>

    <div class="body-content">
    <h1>Dashboard</h1>
<table style="width: 100%">
    <tr>
        <th>CUSTOMER</th>
        <th>OPEN</th>
        <th>PRIME</th>
        <th>SHIPPED</th>
        <th>PENDING FULFILLMENT</th>
        <th>COMPLETED</th>
        <th>ERROR</th>
    </tr>
    <tr>
        <tb

    </tr>
</table>
</div>
