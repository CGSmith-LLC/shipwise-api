<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * QZ Print Plugin asset bundle.
 * @see https://qz.io/
 */
class QZAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/plugins/qz/dependencies/rsvp-3.1.0.min.js', // ECMAScript 6 Promise lib
        'js/plugins/qz/dependencies/sha-256.min.js', // SHA-256 hashing lib
        'js/plugins/qz/qz-tray.js', // QZ Tray websocket wrapper
    ];
}
