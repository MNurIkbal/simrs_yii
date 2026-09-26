<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\components\DocoProcessExtension;

class DocoFTP extends Component
{

    protected $_iniFTP;

    public function __construct()
    {
        $iniFile = @parse_ini_file('../config/env/.env', true);
        $this->_iniFTP = isset($iniFile['ftp']) ? $iniFile['ftp'] : [];
    }

    public function path()
    {
        return [
            'username' => empty($this->_iniFTP['username']) ? 'mhjs.bsl' : $this->_iniFTP['username'],
            'password' => empty($this->_iniFTP['password']) ? 'Mayapada123!!' : $this->_iniFTP['password'],
            'ip_server' => empty($this->_iniFTP['ip_server']) ? '10.2.1.110' : $this->_iniFTP['ip_server']
        ];
    }

}
