<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Tags input for Bootstrap 3
 * @package frontend\assets
 * @see https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
 */
class TagsInputAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/plugins/tagsinput/bootstrap-tagsinput.min.css',
    ];

    public $js = [
        'js/plugins/tagsinput/bootstrap-tagsinput.min.js',
    ];

    public $depends = [
        // depends on jQuery
        'yii\web\YiiAsset',
    ];
}
