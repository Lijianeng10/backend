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
        'css/amazeui.min.css',
        'css/admin.css',
        'css/app.css',
        'css/content.css',
        'css/style.css',
        'css/pagination.css',
        'css/jedate.css',
        'css/jquery.magnify.css',
        'css/easyui.css',
        'css/icon.css',
    ];
    public $js = [
        'js/echarts.min.js',
        'js/amazeui.min.js',
        'js/iscroll.js',
        'js/app.js',
        'js/content.js',
        'js/jquery.jedate.js',
        'js/Ecalendar.jquery.min.js',
        'js/jquery.pagination.js',
        'js/jquery.easyui.min.js',
        'js/tableExport.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
