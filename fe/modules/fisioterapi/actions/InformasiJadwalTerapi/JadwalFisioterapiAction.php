<?php

namespace Doco\fisioterapi\actions\InformasiJadwalTerapi;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Exception;

class JadwalFisioterapiAction extends BaseCurrentAction
{
    public function run()
    {
        $pegawaiId = Yii::$app->request->get('pegawai_id');
        if(!$pegawaiId || !isset($pegawaiId) || is_null($pegawaiId) || empty($pegawaiId)){
            throw new Exception("Payload tidak sesuai", 1);
        }
        return $this->controller->renderAjax('/informasi-jadwal-terapi/jadwal-terapi.php', [
            'pegawaiId' => $pegawaiId
        ]);
    }
}
