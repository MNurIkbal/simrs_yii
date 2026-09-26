<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\View;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FontPickerAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'js/font-icon-picker/demo/style.css',
        'js/font-icon-picker/css/jquery.fonticonpicker.min.css',
        'js/font-icon-picker/themes/grey-theme/jquery.fonticonpicker.grey.min.css',
        'js/font-icon-picker/themes/dark-grey-theme/jquery.fonticonpicker.darkgrey.min.css',
        'js/font-icon-picker/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css',
        'js/font-icon-picker/themes/inverted-theme/jquery.fonticonpicker.inverted.min.css',
    ];

    public $js = [

    ];

    public function init() {
        $this->jsOptions['position'] = View::POS_END;
        parent::init();
    }
}
