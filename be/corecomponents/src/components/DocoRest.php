<?php

namespace Doco\components;


use Yii;
use yii\base\Component;
use GuzzleHttp\Client;
use Doco\components\DocoHelpers;

class DocoRest extends Component
{

    protected $url;
    protected $token;
    protected $modul_id;
    protected $iniFile;
    protected $user_agent;
    protected $instalasi_id;
    protected $ruangan_id;

    public function __construct()
    {
        if (php_sapi_name() != "cli") {
            $jwt = Yii::$app->jwt;
            $token = $jwt->token;
            $modul = $jwt->modul_id;
            $instalasi_id = $jwt->instalasi_id;
            $ruangan_id = $jwt->ruangan_id;
            $this->token = $token;
            $this->modul_id = DocoHelpers::encrypt($modul);
            $this->instalasi_id = $instalasi_id;
            $this->ruangan_id = $ruangan_id;
            $this->iniFile = @parse_ini_file('../../config/env/.api', true);
            $this->user_agent = $this->getBrowser();
        } else {
            $this->iniFile = @parse_ini_file('../config/env/.api', true);
            $this->user_agent = 'CLI';
        }
    }

    /**
    ** @return GuzzleHttp\Client
    **/

    protected function guzzle()
    {
        $header = [
                    'Authorization' => 'Bearer ' . $this->token,
                    'user-agent' => $this->user_agent,
                    'X-Modul-Id' => $this->modul_id,
                    'X-Instalasi-Id'=>$this->instalasi_id,
                    'X-Ruangan-Id'=>$this->ruangan_id,
                ];
        if (YII_ENV == 'dev') {
            $header['X-Owner'] = 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ';
        }
        return new Client([
                'base_uri' => $this->url,
                'headers' => $header
                ]);
    }

    protected function getBrowser() 
    { 
        $u_agent = @$_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } elseif (preg_match('/Firefox/i',$u_agent)) { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } elseif(preg_match('/OPR/i',$u_agent)){ 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } elseif (preg_match('/Chrome/i',$u_agent)) { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } elseif(preg_match('/Safari/i',$u_agent)) { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } elseif(preg_match('/Netscape/i',$u_agent)) { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } else {
            $bname = 'Google Chrome'; 
            $ub = "Postman"; 
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= isset($matches['version'][0]) ? $matches['version'][0] : null;
            } else {
                $version= isset($matches['version'][1]) ? $matches['version'][1] : null;
            }
        } else {
            $version= isset($matches['version'][0]) ? $matches['version'][0] : null;
        }

        if ($version==null || $version=="") {$version="?";}

        return $bname . " Version: " . $version . " on " . $platform;
    }

    public function getBaseUri($property){
        $ini = isset($this->iniFile['internal']) ? $this->iniFile['internal'] : [];
        return isset($ini[$property]) ? $ini[$property] : '';
    }
    
    /**
    *  [membuat object yang di seting pada env/.api file dengan url yang sudah di setting]
    **/

    public function __get($property)
    {
      $ini = isset($this->iniFile['internal']) ? $this->iniFile['internal'] : [];
      if (property_exists($this, $property)) {
        return $this->$property;
      } else {
        $this->url = isset($ini[$property]) ? $ini[$property] : '';

        return $this->$property = $this->guzzle();
      }
    }

    public function __set($name, $value)
    {

    }

}