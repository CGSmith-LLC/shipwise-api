<?php

use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Invoice #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Billing', 'url' => ['/billing']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/invoice.css');
$this->registerCssFile('/css/invoice-print.css', ['media' => 'print']);

$formatter = Yii::$app->getFormatter();
?>
<div class="invoice-view">

    <div class="invoice-toolbar text-right">
        <?= ButtonDropdown::widget(
            [
                'label'         => 'Print',
                'options' => ['class' => 'btn dropdown-toggle btn-primary'],
                'dropdown'      => [
                    'items' => [
                        [
                            'label'       => 'Invoice',
                            'url'         => ['invoice-pdf', 'id' => $model->id],
                            'linkOptions' => ['target' => '_blank'],
                        ],
                        [
                            'label'       => 'Receipt',
                            'url'         => ['receipt-pdf', 'id' => $model->id],
                            'linkOptions' => ['target' => '_blank'],
                        ],
                    ],
                ],
            ]
        ) ?>
    </div>

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
                                Due: <?php
                                $date = new DateTime($model->due_date);
                                echo $date->format('F jS, Y');
                                ?><br>
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
                                <?= Yii::$app->params['invoicing']['company'] ?><br>
                                <?= Yii::$app->params['invoicing']['email'] ?><br>
                                <?= Yii::$app->params['invoicing']['phone'] ?>
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
