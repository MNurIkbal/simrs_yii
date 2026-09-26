<?php
namespace app\components;

use Yii;

/**
 * Class untuk bantu bantu debug flow sistem.
 * @author rbs1518 (rinardi@docotel.com)
 */
class DebugHelper
{

    public static function dump($var)
    {
        //$filePath = (Yii::$app->basePath.'/runtime/logs/debug.log');        
        ob_start();
        var_dump($var);
        $s = ob_get_contents();
        ob_end_clean();

        $filePath = (Yii::getAlias('@runtime').'/logs/debug.log');
        file_put_contents($filePath,$s."\n",FILE_APPEND);
    }

    public static function write($text)
    {        
        $txt .= "\n";        

        $filePath = (Yii::getAlias('@runtime').'/logs/debug.log');
        file_put_contents($filePath,$txt,FILE_APPEND);
    }

}
?>