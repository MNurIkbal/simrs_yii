<?php

namespace Doco\models;

class ObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * define function name
     * 
     * @param Integer penjamin_id (used as param for function)
     * @param Integer kelaspelayanan_id (used as param for function)
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function functionName()
    {
        return 'infostokobatalkes_fn';
    }
}
