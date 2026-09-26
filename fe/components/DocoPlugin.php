<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\components\DocoProcessExtension;

class DocoPlugin extends Component
{

    protected $_iniFile;

    public function __construct()
    {
        $iniFile = @parse_ini_file('../config/env/.extension', true);
        $this->_iniFile = isset($iniFile['extensions']) ? $iniFile['extensions'] : [];
    }

    public function execute($controller,$type)
    {
        $type = strtolower($type);
        $modulId = $controller->module->id;
        $extensions = isset($this->_iniFile["{$modulId}:{$type}"]) ? $this->_iniFile["{$modulId}:{$type}"] : null;
        
        if (empty($extensions)) {
            $type = str_replace('_', '', ucwords($type, '_'));
            $extensions = "app\modules\\{$modulId}\\processes\\{$type}Process";
            $classFile = Yii::getAlias("@".str_replace("\\","/",$extensions).".php");
            if(file_exists($classFile)) {
                return (new DocoProcessExtension(new $extensions,$controller))->run();
            }else{
                $extensions = "app\processes\\{$type}Process";
                // $classFile = Yii::getAlias("@".str_replace("\\","/",$extensions).".php");
                return (new DocoProcessExtension(new $extensions,$controller))->run();
            }
        } 
        return (new DocoProcessExtension(new $extensions,$controller))->run();
    }

}