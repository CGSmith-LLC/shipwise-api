<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\Pjax;
use frontend\assets\QZAsset;

/* @var $this yii\web\View */
/* @var $model common\models\BulkAction */

$this->title = $model->name;
YiiAsset::register($this);
QZAsset::register($this);
?>
    <div class="bulk-action-view">

        <div id="qz-connection" class="label label-default">
            QZ printer status: <span id="qz-status"></span>
        </div>

        <?php Pjax::begin([
            'id' => 'pjax-container',
        ]);

        if ($model->isCompleted()) {
            $this->registerJs("stopRefreshing();", View::POS_END, 'bulk-result-handler-end');
        }
        ?>

        <div class="jumbotron">
            <p class="lead"><?= $model->name ?></p>
            <?php
            $btnText  = $model->isProcessing() ? 'Processing..' : 'Re-print';
            $disabled = $model->isProcessed() ? '' : 'disabled';
            ?>
            <div>
                <?= Html::a($btnText, false,
                    [
                        'onclick' => 'reprint()',
                        'class'   => "btn btn-lg btn-default $disabled",
                        'style'   => 'min-width:170px;',
                    ]) ?>
            </div>
        </div>

        <h4>
            Bulk action status is: <span class="label label-<?= $model->statusColor ?>"><?= $model->statusName ?></span>

            <?php if ($model->isProcessing()) :
                $sinceStart = $model->processingSince(); ?>
                <span class="small"> since <?= $sinceStart->i ?> min <?= $sinceStart->s ?> sec</span>
            <?php endif; ?>
        </h4>
        <hr/>

        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query'      => $model->getItems()->orderBy('order_id'),
                'sort'       => false,
                'pagination' => false,
            ]),
            'tableOptions' => ['class' => 'table table-striped table-hover'],
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                //'order_id',
                'order.customer_reference',
                //'order.address.name',
                'job',
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return '<span class="label label-' . $model->statusColor . '"> ' . $model->statusName . '</span>';
                    },
                ],
                [
                    'label'  => 'Print status',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        if ($model->isDone()) {
                            return '<span class="label label-success">Pushed</span>'
                                . "<script>pushDoc('{$model->id}', '{$model->base64_filedata}', '{$model->base64_filetype}');</script>";
                        } else {
                            return '<span class="label label-warning">Waiting</span>';
                        }
                    },
                ],
                [
                    'attribute' => 'errors',
                    'format'    => 'raw',
                    'value'     => function ($model) {
                        return '<span class="label label-danger">' . $model->errors . '</span>';
                    },
                ],
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>

    </div>

<?php
$this->registerJs("
    var refreshIntervalId,
        docsToPrint = {},
        printerToFind = 'rollo',
        printerFound,
        qz;
    
    function stopRefreshing() {
        clearInterval(refreshIntervalId);
    }
    
    // Launch QZ
    function launchQZ() {
        if (!qz.websocket.isActive()) {
            window.location.assign(\"qz:launch\");
            // Retry 5 times, pausing 1 second between each attempt
            startConnection({ retries: 5, delay: 1 });
        }
    }
    
    // QZ connection
    function startConnection(config) {
        if (!qz.websocket.isActive()) {
            updateState('Waiting', 'default');

            qz.websocket.connect(config).then(function() {
                findPrinter();
                updateState('Active', 'success');
            }).catch(handleConnectionError);
        } else {
            updateState('An active connection with QZ already exists.', 'warning');
        }
    }
    
    // QZ find printer
    function findPrinter() {
        qz.printers.find(printerToFind).then(function(found) {
           printerFound = found;
        }).catch(handleConnectionError);
    }
    
    // QZ state
    function updateState(text, css) {
        $(\"#qz-status\").html(text);
        $(\"#qz-connection\").removeClass().addClass('label label-' + css);
    }
    
    // QZ Helpers
    function handleConnectionError(err) {
        updateState('Error', 'danger');

        if (err.target != undefined) {
            if (err.target.readyState >= 2) { //if CLOSING or CLOSED
                updateState(\"Connection to QZ Tray was closed\", 'danger');
            } else {
                updateState(\"A connection error occurred, check log for details\", 'danger');
                console.error(err);
            }
        } else {
            updateState(err, 'danger');
        }
    }
    
    // Push document to global array `docsToPrint` if not already exists
    function pushDoc(key, docData, docType) {
        if (docsToPrint[key]) {
            return; // already exists 
        } else {
            docsToPrint[key] = {\"type\": docType, \"data\": docData};
            printDoc(key, docData, docType);
        }
    }
    
    // Print document
    function printDoc(key, docData, docType) {
        console.log('printDoc: ' + key + ' ' + docType);
        
        var config = qz.configs.create(printerFound);
        var data = [{
           type: docType.toLowerCase(),
           format: 'base64',
           data: docData 
        }];
        qz.print(config, data).catch(function(e) { console.error(e); });
    }
    
    // Reprint all documents
    function reprint() {
        $.each(docsToPrint, function(key, doc) {
            printDoc(key, doc.data, doc.type);
        });
    }

    ", View::POS_BEGIN,
    'bulk-result-handler-begin'
);

$this->registerJs("
    refreshIntervalId = setInterval(function(){ 
        $.pjax.reload({container: '#pjax-container', async: false}); 
    }, 4000);
    
    launchQZ();

    ", View::POS_READY,
    'bulk-result-handler-ready'
);
