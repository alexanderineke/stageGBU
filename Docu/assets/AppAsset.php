<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'themes/dcu/assets/css/font-awesome.css',
        'themes/dcu/assets/css/gbu.css',
        'themes/dcu/assets/css/bootstrap-responsive.min.css',
        'themes/dcu/assets/css/bootstrap.css',
        'themes/dcu/assets/images/favicon.png'
    ];
    public $js = [
        'themes/dcu/assets/js/modernizr.custom.js',
       'themes/dcu/assets/js/bootstrap.min.js',
       // 'themes/dcu/assets/js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
