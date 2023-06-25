<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/media.css',
    ];
    public $js = [
        'assets/js/site.js',
        'https://cdn.jsdelivr.net/npm/jquery.equalheights@1.5.3/jquery.equalheights.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\jui\JuiAsset'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
