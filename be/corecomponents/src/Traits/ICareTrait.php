<?php

namespace Doco\Traits;

use Yii;
use Doco\models\Pegawai;
use Doco\models\bpjs\ICare;

/**
 * Trait of Nursing Note
 */
trait ICareTrait {
    public function getUrlIcare() {
        $pegawai = Pegawai::find()->select(['pegawai_id', 'kode_dokter_bpjs'])
            ->where(['pegawai_id' => Yii::$app->request->post('pegawai_id')])
            ->asArray()->one();

        $payload = [
            'param' => Yii::$app->request->post('identifier'),
            'kodedokter' => (int) $pegawai['kode_dokter_bpjs']
        ];

        $icare = (new ICare)->getUrlIcare($payload);
        $icareRes = $icare['metaData'];
        if($icare['metaData']['code'] != 200) {
            return $this->responseJson($icareRes['code'], $icareRes['message']);
        }

        return $this->responseJson($icareRes['code'], $icare['response']['url']);
    }
}
