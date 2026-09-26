<?php
namespace app\components;
use Yii;
use yii\web\Session;

class InterceptedSession extends Session
{    
    private $_Temporer = [];

    private $_Inclusions = [
        'moduleID',
        'active_workspace',
        'menu',
        'akses_menu'
    ];

    private $_SpecialCases = [
        'loket'
    ];

    public function reloadSpecialCases()
    {
        foreach ($this->_SpecialCases as $itemKey)
        {
            $val = $this->get('is_aw_sc_'.$itemKey);
            if ($val !== null)
                $this->_Temporer['active_workspace'][$itemKey] = $val;
        }
    }

    public function get($key, $defaultValue = null)
    {
        if (in_array($key,$this->_Inclusions))
        {
            if (isset($this->_Temporer[$key]))
                return $this->_Temporer[$key];
            else $defaultValue;
        }
        else
            return parent::get($key,$defaultValue);
    }

    public function set($key, $value)
    {
        if (in_array($key,$this->_Inclusions))
        {                        
            $this->_Temporer[$key] = $value;
            if ($key == 'active_workspace')
            {
                foreach ($this->_SpecialCases as $itemKey)
                {
                    if (isset($value[$itemKey]))
                        parent::set('is_aw_sc_'.$itemKey,$value[$itemKey]);
                }
            }
        }
        else
            parent::set($key,$value);
    }

    public function remove($key)
    {
        if (in_array($key,$this->_Inclusions))
        {
            if (isset($this->_Temporer[$key]))
            {
                $temp = $this->_Temporer[$key];
                unset($this->_Temporer[$key]);
                return $temp;
            }                
            else
                return null;
        }   
        else
            return parent::remove($key);
    }

    public function removeAll()
    {
        $this->_Temporer = [];
        parent::removeAll();
    }

    public function has($key)
    {
        if (in_array($key,$this->_Inclusions))
            return (isset($this->_Temporer[$key]));
        else
            return parent::has($key);
    }
}
?>