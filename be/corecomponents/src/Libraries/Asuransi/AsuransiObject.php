<?php

namespace Doco\Libraries\Asuransi;

use Doco\Libraries\Asuransi\Models\Penjamin;

class AsuransiObject
{
    public $penjamin;

    public $attribute;

    public static $instance;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return isset($this->attribute[$property]) ? $this->attribute[$property] : null;
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new AsuransiObject();
        }
        return self::$instance;
    }

    public function setAttributes(array $configProvider)
    {
        $this->attribute = $configProvider;
    }
}
