<?php

namespace app\assets;

use yii\web\AssetBundle;

class SirsTableAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => \yii\web\View::POS_END];

    public $js = [
    	'components/sirstable/elements.js'
    ];

    public $css = [
    	'components/sirstable/styles.css',
    	'components/sirstable/font.css'
    ];
}