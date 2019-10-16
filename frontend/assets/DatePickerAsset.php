<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Datepicker for Bootstrap plugin asset bundle.
 */
class DatePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';
    public $css      = [
        'css/plugins/datapicker/datepicker3.css',
    ];
    public $js       = [
        'js/plugins/datapicker/bootstrap-datepicker.js',
    ];
    public $depends  = [
        // depends on jQuery
        'yii\web\YiiAsset',
    ];
}
