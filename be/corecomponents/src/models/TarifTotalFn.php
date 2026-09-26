<?php

namespace Doco\models;

class TarifTotalFn extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * Define function name
     * 
     * @return String
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function functionName()
    {
        return 'tariftotalrs_fn';
    }

    public static function primaryKey()
    {
      return ["tariftindakan_id"];
    }
}
