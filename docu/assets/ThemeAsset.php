<?php

namespace app\assets;

use yii\web\AssetBundle;

class ThemeAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        '/themes/dcu/assets/css/bootstrap.css',
        '/themes/dcu/assets/css/bootstrap-responsive.min.css',
        '/themes/dcu/assets/css/gbu.css',
        '/themes/dcu/assets/css/font-awesome.css',
        'themes/dcu/assets/images/favicon.png'
    ];
    public $js = [
    ];
    public $jsOptions = [
        'themes/dcu/assets/js/modernizr.custom.js',
        'themes/dcu/assets/js/bootstrap.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}

?>