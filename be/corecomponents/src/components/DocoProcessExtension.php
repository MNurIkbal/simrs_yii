<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;


use Yii;

class DocoProcessExtension 
{
    private $extensible;

    public function __construct(IProcessExtension $extensible)
    {
        $this->extensible = $extensible;
    }

    /**
     * @return array
     */
    public function run() 
    {
        return $this->extensible->execute();
    }
}