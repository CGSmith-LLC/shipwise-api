<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Invoice #' . $model->id;



$formatter = Yii::$app->getFormatter();
?>
<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    .label {
        font-size: 100%;
    }

    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
</style>
<div class="invoice-view">

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="<?php echo Yii::$app->getUrlManager()->createAbsoluteUrl('/images/invoice-logo.png') ?>" style="width:100%; max-width:200px;">
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
