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
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
        'templete/login/assets/css/style.css',
        'templete/limitless/assets/css/icons/icomoon/styles.css',
        'templete/limitless/assets/css/bootstrap.css',
        'templete/limitless/assets/css/core.css',
        'templete/limitless/assets/css/components.css',
        'templete/limitless/assets/css/colors.css',
        'templete/limitless/assets/css/icons/fontawesome/styles.min.css',
        'build/bundle.css'
    ];
    public $js = [
        'templete/limitless/assets/js/plugins/loaders/pace.min.js',
        'templete/limitless/assets/js/core/libraries/jquery.min.js',
        'templete/limitless/assets/js/core/libraries/bootstrap.min.js',
        'templete/limitless/assets/js/plugins/loaders/blockui.min.js',
        'templete/limitless/assets/js/plugins/ui/nicescroll.min.js',
        'templete/limitless/assets/js/plugins/ui/drilldown.js',
        'templete/limitless/assets/js/plugins/forms/selects/select2.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
