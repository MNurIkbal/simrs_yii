<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

use Yii;
use yii\base\Component;
use Doco\components\DocoProcessExtension;

class DocoPlugin extends Component
{

    protected $_iniFile;

    public function __construct()
    {
        $iniFile = @parse_ini_file(dirname(dirname(dirname(dirname(__FILE__)))).'/config/env/.api', true);
        $this->_iniFile = isset($iniFile['extensions']) ? $iniFile['extensions'] : [];
    }

    public function execute($type)
    {
        $type = strtolower($type);
        $params = Yii::$app->params;
        $nameService = isset($params['service']) ? strtolower($params['service']) : null;
        $extensions = isset($this->_iniFile["{$nameService}:{$type}"]) ? $this->_iniFile["{$nameService}:{$type}"] : null;

        if (empty($extensions)) {
            $type = str_replace('_', '', ucwords($type, '_'));
            $extensions = "Doco\\processes\\{$type}Process";
        } 
        return (new DocoProcessExtension(new $extensions))->run();
    }

    // digunakan untuk mengambil class Extension
    public function getExtension($type)
    {
        $type = strtolower($type);
        $params = Yii::$app->params;
        $nameService = isset($params['service']) ? strtolower($params['service']) : null;
        $fullNameService = strpos($type, ':') >= 0 ? $type : "{$nameService}:{$type}";
        Yii::error($fullNameService);
        $extensions = isset($this->_iniFile[$fullNameService]) ? $this->_iniFile[$fullNameService] : null;
        Yii::error($extensions);

        if (empty($extensions)) {
            $type = str_replace('_', '', ucwords($type, '_'));
            $extensions = "Doco\\processes\\{$type}Process";
        }

        return $extensions;
    }

    public function checkExtension($type) {
        $type = strtolower($type);
        $params = Yii::$app->params;
        $nameService = isset($params['service']) ? strtolower($params['service']) : null;
        $extensions = isset($this->_iniFile["{$nameService}:{$type}"]) ? $this->_iniFile["{$nameService}:{$type}"] : null;

        $hasExtensions = true;

        if (empty($extensions)) {
            $type = str_replace('_', '', ucwords($type, '_'));
            $extensions = "Doco\\processes\\{$type}Process";
            $hasExtensions = false;
        } 

        return $hasExtensions;
    }

}