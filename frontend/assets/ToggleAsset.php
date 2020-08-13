<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Toggle for Bootstrap plugin asset bundle.
 */
class ToggleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/plugins/toggle/bootstrap-toggle.css',
    ];
    public $js = [
        'js/plugins/toggle/bootstrap-toggle.js',
    ];
    public $depends = [
        // depends on jQuery
        'yii\web\YiiAsset',
    ];
}
