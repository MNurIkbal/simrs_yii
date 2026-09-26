<?php
namespace app\components\object;

/**
 * 
 */

use app\components\contracts\ObjectInterface;

class DocoBaseObject extends \yii\base\BaseObject implements ObjectInterface
{
    public function buildArray()
    {
        return [];
    }
}