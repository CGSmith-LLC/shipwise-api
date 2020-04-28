<?php

use common\models\Order;
use common\models\Status;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">

    <div class="jumbotron">
        <?php if (Yii::$app->user->identity->isAdmin) : ?>

            <h1>Welcome Admin!</h1>
            <p class="lead">You have the power <span class="glyphicon glyphicon-sunglasses"></span></p>
            <p><?= Html::a('Manage Users', ['/user/admin'], ['class' => 'btn btn-success']) ?></p>

        <?php else : ?>

            <h1>Welcome!</h1>
            <p class="lead">To your ShipWise dashboard.</p>
            <p><?= Html::a('Get started', ['/order'], ['class' => 'btn btn-success']) ?></p>

        <?php endif; ?>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','Open Orders')?></h5>
            <h6 class="card-subtitle mb-2"><?= $orders ?></h6>
        </div>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','Total Pending Orders')?></h5>
            <h6 class="card-subtitle mb-2"><?= $totalpendingorders ?></h6>
        </div>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','Shipped')?></h5>
            <h6 class="card-subtitle mb-2"><?= $shipped ?></h6>
        </div>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','Cancelled')?></h5>
            <h6 class="card-subtitle mb-2"><?= $cancelled ?></h6>
        </div>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','WMS Errors')?></h5>
            <h6 class="card-subtitle mb-2"><?= $wmserrors ?></h6>
        </div>
    </div>

    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><?=Yii::t('app','Completed')?></h5>
            <h6 class="card-subtitle mb-2"><?= $completed ?></h6>
        </div>
    </div>


        <br>
        <br>
        <br>
        <br>

            <script>
                window.onload = function () {

                    var dataPoints = [];

                    var chart = new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true,
                        theme: "light2",
                        zoomEnabled: true,
                        title: {
                            text: "Quick View"
                        },
                        axisY: {
                            title: "Price in USD",
                            titleFontSize: 24,
                            prefix: "$"
                        },
                        data: [{
                            type: "line",
                            yValueFormatString: "$#,##0.00",
                            dataPoints: dataPoints
                        }]
                    });

                    function addData(data) {
                        var dps = data.price_usd;
                        for (var i = 0; i < dps.length; i++) {
                            dataPoints.push({
                                x: new Date(dps[i][0]),
                                y: dps[i][1]
                            });
                        }
                        chart.render();
                    }

                    $.getJSON("https://canvasjs.com/data/gallery/php/bitcoin-price.json", addData);

                }
            </script>
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
        <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
