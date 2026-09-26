<?php

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper;
use app\models\reseptur\GeneralResepturForm;
use Doco\apotek\components\filler\ResepDetailFiller;

class CetakMultipleResep extends Action {
    // $id = resep_id (if reseptur_id is empty) / reseptur_id
    // $nomor = nomor resep / reseptur
    public function run($id, $nomor = null) {
        $title = 'Cetak multiple obat';
        $reseptur_id = DocoHelpers::decrypt($id);
        $modelReseptur = new GeneralResepturForm;
        $data_resep = [];
        $_requestDataResep = Yii::$app->docoRest->apotek->get('allow/info-resep',[
            'query' => [
                'nomor' => $nomor, // nomor resep / reseptur
            ]
        ]);
        $_responDataResep = json_decode($_requestDataResep->getBody(),true);
        $data_resep = $_responDataResep['response']['data'];
        $status_resep = $data_resep['status_reseptur_id'];
        $nomor = DocoHelpers::encrypt(HandlingValueHelper::nullValue($nomor));
        $waktuPemberian = [
            'Pagi' =>'Pagi',
            'Siang' =>'Siang',
            'Sore' =>'Sore',
            'Malam' =>'Malam',
        ];
        $keteranganPemberian = [
            'Sebelum Makan' =>'Sebelum Makan',
            'Sesudah Makan' =>'Sesudah Makan',
            'Saat Makan' =>'Saat Makan',
        ];


        return $this->controller->renderAjax('_modal_cetak_multi_resep', get_defined_vars());
    }
}
