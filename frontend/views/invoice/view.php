<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Invoice #' . $model->id;

$this->registerCssFile('/css/invoice.css');
$this->registerCssFile('/css/invoice-print.css', ['media' => 'print']);

$formatter = Yii::$app->getFormatter();
?>
<div class="invoice-view">

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="\images\invoice-logo.png" style="width:100%; max-width:200px;">
                            </td>

                            <td>
                                <?= Html::encode($this->title) ?><br>
                                Due: <?= $model->due_date ?><br>
                                <?=$model->getStatusLabel()?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <?= $model->customer_name ?><br>
                            </td>

                            <td>
                                ShipWise<br>
                                support@getshipwise.com<br>
                                (262) 342-6638
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>
                    Item
                </td>

                <td>
                    Price
                </td>
            </tr>

            <?php

            /**
             * Loop through items and display them on the invoice
             * @var \common\models\InvoiceItems $item
             */
            foreach ($model->getItems()->all() as $item) { ?>
                <tr class="item">
                    <td>
                        <?= $item->name ?>
                    </td>

                    <td>
                        <?= $formatter->asCurrency($item->getDecimalAmount()) ?>
                    </td>
                </tr>
                <?php
            }
            ?>


            <tr class="total">
                <td></td>

                <td>
                    Total: <?= $formatter->asCurrency($model->getDecimalAmount()) ?>
                </td>
            </tr>
        </table>
    </div>
</div>
