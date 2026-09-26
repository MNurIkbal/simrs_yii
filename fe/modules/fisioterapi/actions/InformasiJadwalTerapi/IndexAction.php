<?php

namespace Doco\fisioterapi\actions\InformasiJadwalTerapi;

use Yii;
use app\components\DHtml;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers;
        $title = $helper->coalesce(DHtml::getTitleMenu(), 'Informasi Jadwal Terapi');
        $terapis = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'soap/get-terapis'
        ]);
        $listPegawai = ArrayHelper::map($terapis, 'pegawai_id', 'nama_pegawai');
        $dataView = [
            'title' => $title,
            'listPegawai' => $listPegawai
        ];
        return Yii::$app->controller->render('index', compact('dataView'));
    }
}
