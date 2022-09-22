<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $title;

?>
<p>
    <h3><?=$this->title?></h3>
<br/>
<?= $message ?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style="border-radius: 2px;" bgcolor="#ED2939">
                        <a href="<?=$url?>" target="_blank" style="padding: 8px 12px; border: 1px solid #ED2939;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                            <?=$urlText?>
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>