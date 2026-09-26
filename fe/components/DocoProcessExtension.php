<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components;


use Yii;

class DocoProcessExtension 
{
    private $extensible;
    private $controller;

    public function __construct(IProcessExtension $extensible, $controller)
    {
        $this->extensible = $extensible;
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function run() 
    {
        return $this->extensible->execute($this->controller);
    }
}