<?php

namespace Extensions\pendaftaran;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\Pendaftaran;
use Doco\models\pendaftaran\PendaftaranOnline;

class DoUpdateTracer extends \Doco\processes\UpdateStatusTracerProcess {
    public function processFlow()
    {
        $pendaftaranol_id  = Yii::$app->request->post('pendaftaranol_id', null);
        $pendaftaranol_id = !is_null($pendaftaranol_id) ? explode(",", $pendaftaranol_id) : []; // set jadi array
        $jenis = Yii::$app->request->post('param', 'pendaftaran');
        if ( !empty($pendaftaranol_id) && $jenis == 'reservasi' ) {
            $pendaftaranol_ids = implode(',', $pendaftaranol_id);
            PendaftaranOnline::updateAll([
                'is_cetaktracer' => 2
            ], 'pendaftaranol_id IN ('. $pendaftaranol_ids .')');
            $pendaftaran_ids = PendaftaranOnline::find()->select(['pendaftaran_id'])->andWhere(['IN','pendaftaranol_id', $pendaftaranol_id])->andWhere(['IS NOT', 'pendaftaran_id', null])->asArray()->all();
            if(!empty($pendaftaran_ids)){
                $arrPendaftaranId = [];
                foreach ($pendaftaran_ids as $key => $value) {
                    $arrPendaftaranId[] = $value['pendaftaran_id'];
                }
                $listPendaftaranId = implode(',', $arrPendaftaranId);
                Pendaftaran::updateAll([
                    'status_konfirmasi' => DocoConstants::STATUS_KONFIRMASIRM_PROSES
                ], 'pendaftaran_id IN ('. $listPendaftaranId .') AND pasienadmisi_id IS NULL');
            }
        }
        return;
    }
}