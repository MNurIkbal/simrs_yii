<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-22 16:27:36
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-22 16:29:30
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;
use yii\base\InvalidConfigException;

class FGetinstruksi extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return 'fgetinstruksi';
    }

    public static function tableName($ext_obj = null)
    {
        if($ext_obj){
            $params = $ext_obj;
        }else{
            $params = self::$_extParam;
        }
        $functionParams = '()';
        if(isset($params)){
            $functionParams = "(".$params.")";
        }else{
            throw new InvalidConfigException('Konfigurasi Property extParam Salah');
        }
        return static::functionName().$functionParams;
    }
}
