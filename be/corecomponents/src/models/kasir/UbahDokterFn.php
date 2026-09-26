<?php

/**
 * @author : Maulana Muhammad Rizky
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

 namespace Doco\models\kasir;

use Yii;

class UbahDokterFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "sp_ubah_dokter_tindakan";
    }
}
