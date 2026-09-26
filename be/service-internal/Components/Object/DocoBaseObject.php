<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Object;

use Integrasi\Components\Contracts\ObjectInterface;

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