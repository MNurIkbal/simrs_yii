<?php

namespace Doco\models;

class TarifTotalKamarFn extends \Doco\components\DocoPostgreFunctionAR
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
        return 'tariftotalkamarrs_fn';
    }

    public static function primaryKey()
    {
      return ["tariftindakan_id"];
    }
}
