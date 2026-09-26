<?php

namespace Doco\models;

use Yii;

class GenerateNoSuratKeteranganFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "generateNoSuratKeterangan";
    }

    public static function generate($surat_keterangan_id) {
        $model = new GenerateNoSuratKeteranganFn(
            ['extParam' => [$surat_keterangan_id]]
        );

        return $model::find()->asArray()->one();
    }

    public static function attributSchema()
    {
        return [];
    }
}
