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
    public function setAttributes(array $data)
    {
        $buildArry = $this->buildArray();
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}