<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'CSV Export Report';

$formatter = Yii::$app->getFormatter();
$startDate = $formatter->asDate($start_date, 'php:l, F j, Y');
$endDate = $formatter->asDate($end_date, 'php:l, F j, Y');
?>
<p>Hello,<br/>
<br/>
Please <a href="<?=$url?>">download your requested CSV order report</a> for <?=$customerName?> from <?=$startDate?> to <?=$endDate?> below.<br/><br/>
