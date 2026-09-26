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
class AntrianAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'templete/limitless/assets/css/icons/fontawesome/styles.min.css',
        'templete/limitless/assets/css/icons/icomoon/styles.css',
        'templete/limitless/assets/css/bootstrap.css',
        'templete/limitless/assets/css/core.css',
        'templete/limitless/assets/css/components.css',
        'templete/limitless/assets/css/colors.css',
        'css/keyboard.min.css',
        'build/bundle.css'
    ];
    public $js = [
        'templete/limitless/assets/js/plugins/loaders/pace.min.js',
        'templete/limitless/assets/js/core/libraries/bootstrap.min.js',
        'templete/limitless/assets/js/plugins/loaders/blockui.min.js',
        'templete/limitless/assets/js/plugins/ui/drilldown.js',
        'templete/limitless/assets/js/plugins/ui/nicescroll.min.js',

        /*THEME*/
        'templete/limitless/assets/js/plugins/forms/styling/uniform.min.js',
        'templete/limitless/assets/js/plugins/forms/styling/switchery.min.js',
        'templete/limitless/assets/js/plugins/forms/inputs/touchspin.min.js',
        'templete/limitless/assets/js/plugins/forms/wizards/steps.min.js',
        'templete/limitless/assets/js/plugins/forms/wizards/stepy.min.js',
        'templete/limitless/assets/js/plugins/forms/validation/validate.min.js',
        'templete/limitless/assets/js/plugins/notifications/pnotify.min.js',
        'js/jqClock.min.js',
        'js/jquery.redirect.js',
        'js/jquery.keyboard.min.js',
        'js/socket.io.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
