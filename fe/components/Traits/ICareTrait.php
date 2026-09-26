<?php

namespace app\components\Traits;

use Yii;
use app\components\DocoConstants;

trait ICareTrait {
    public function actionModalIcare() {
        switch (Yii::$app->docoVars->workspace('instalasi_id')) {
            case DocoConstants::INSTALASI_ID_RJ:
                $rest = Yii::$app->docoRest->rajal;
                $url = 'tra-pemeriksaan/icare';
                break;
            case DocoConstants::INSTALASI_ID_RD:
                $rest = Yii::$app->docoRest->igd;
                $url = 'pemeriksaan-igd/icare';
                break;
            case DocoConstants::INSTALASI_ID_RI:
                $rest = Yii::$app->docoRest->ranap;
                $url = 'pemeriksaan-rawat-inap/icare';
                break;
        }

        $getUrlIcare = $this->guzzleExec($rest, [
            'url' => $url,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'identifier' => Yii::$app->request->get('icare_identifier'),
                    'pegawai_id' => Yii::$app->request->get('id_pegawai'),
                ]
            ]
        ]);

        $dataPasien = [
            'no_rekam_medik' => Yii::$app->request->get('no_rekam_medik'),
            'no_pendaftaran' => Yii::$app->request->get('no_pendaftaran'),
            'nama' => Yii::$app->request->get('nama_pasien'),
        ];

        return $this->renderAjax('//icare/_preview_icare', [
            'statusCode' => $getUrlIcare['meta']['code'],
            'errorMessage' => $getUrlIcare['meta']['code'] != 200 ? $getUrlIcare['message'] : '',
            'title' => 'Preview i-Care | ' . implode(" / ", $dataPasien),
            'path' => isset($getUrlIcare['message']) ? $getUrlIcare['message'] : '',
        ]);
    }
}
