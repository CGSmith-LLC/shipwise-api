<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AliasItemAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/plugins/aliasitem/create.js',
    ];
    public $depends = [
        'yii\web\YiiAsset', // depends on jQuery
    ];

}