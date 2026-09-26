<?php

/**
 * @author : Maulana Muhammad Rizky
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

 namespace Doco\models\kasir;

use Yii;

class RecalculateTagihanFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "sp_recalculate_tagihan";
    }
}
