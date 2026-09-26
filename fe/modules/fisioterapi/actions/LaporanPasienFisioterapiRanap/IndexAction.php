<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers;
        $title = $helper->coalesce(DHtml::getTitleMenu(), Yii::t('fe', 'Laporan Pasien Fisioterapi Rawat Jalan'));
        $listDokterAll = [];
        $listDokterFisio = [];
        $status = [];
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-pasien-fisioterapi-ranap/get-attributes',
                'payload' => []
            ]);
            $dataResponse = ArrayHelper::getValue($response, 'data');
            if ($response) {
                $dokter = ArrayHelper::getValue($dataResponse, 'dokter', []);
                $dokterFisio = ArrayHelper::getValue($dataResponse, 'dokter_fisio', []);
            }
            if ($dokterFisio) {
                $listDokterFisio[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                foreach ($dokterFisio as $value) {
                    $listDokterFisio[] = [
                        'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                        'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                    ];
                }
            }
            if ($dokter) {
                $listDokterAll[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                foreach ($dokter as $value) {
                    $listDokterAll[] = [
                        'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                        'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                    ];
                }
            }
            $dataView = [
                'title' => $title,
                'listDokter' => $listDokterAll,
                'listDokterFisio' => $listDokterFisio,
            ];
            return Yii::$app->controller->render('index', compact('dataView'));
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
