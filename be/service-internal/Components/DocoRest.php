<?php

namespace Integrasi\Components;


use Yii;
use yii\base\Component;
use GuzzleHttp\Client;
use Doco\components\DocoHelpers;

class DocoRest extends Component
{

    protected $url;
    protected $token;
    protected $iniFile;
    protected $user_agent;
    protected $owner;

    public function __construct()
    {
        $this->iniFile = @parse_ini_file(__DIR__. '/../../config/env/.api', true);
        $this->user_agent = php_sapi_name();
    }

    /**
    ** @return GuzzleHttp\Client
    **/

    protected function guzzle()
    {
        $header = [
                    'Authorization' => $this->token,
                    'user-agent' => $this->user_agent,
                    'X-Owner' => $this->owner,
                ];

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

    public function getBaseUri($property)
    {
        $ini = isset($this->iniFile['internal']) ? $this->iniFile['internal'] : [];
        return isset($ini[$property]) ? $ini[$property] : '';
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     *
     * @return self
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
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