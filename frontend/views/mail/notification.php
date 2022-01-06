<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $message string */
/* @var $reason_general string */
/* @var $reason_specific string */
/* @var $url string */
/* @var $name string */

$this->title = 'Notification';
?>
<p>Hi <?= $name ?>,<br/>
    <br/>
    We are notifying you of <?=$reason_general?> that requires your attention.<br/>
    <br/>
    <?= $message ?><br/>
    <br/>
    <?php if (isset($url)) { ?>
    <a href="<?= $url?>">View <?=$reason_specific?></a>
    <?php } ?>
</p>