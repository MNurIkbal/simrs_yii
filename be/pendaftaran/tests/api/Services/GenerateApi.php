<?php

class GenerateApi 
{
    private static $instance;
    private $testing; 

    public function _before(\ApiTester $I)
    {
        $this->testing = $I;
    }


    public static function generate() {
        if (self::$instance == null) {
            self::$instance = new GenerateApi;
        }
        return self::$instance;
    }

    public function test()
    {
        return $this->testing->getConfig('no_pendaftaran');
    }
}
