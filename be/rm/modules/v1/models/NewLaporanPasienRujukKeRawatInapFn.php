<?php
namespace app\modules\v1\models;

use Yii;

class NewLaporanPasienRujukKeRawatInapFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "new_laporanpasienrujukkeri_fn";
    }

    public function getData($start, $end) {
        return (new NewLaporanPasienRujukKeRawatInapFn(['extParam'=>[$start,$end]]))->find();
    }
}