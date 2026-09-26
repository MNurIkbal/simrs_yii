<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $title = (new DocoHelpers)->coalesce(DHtml::getTitleMenu(), 'Laporan Pemeriksaan Fisioterapi');
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('laporan-pemeriksaan-fisioterapi-ranap/get-attributes');
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            if ($response) {
                $dokterTerapis = ArrayHelper::getValue($response, 'dokter_terapis', []);
            }
            if (is_array($dokterTerapis)) {
                $listDokter[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($dokterTerapis) > 0) {
                    foreach($dokterTerapis as $value) { 
                        $listDokter[] = [
                            'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ]; 
                    }
                }   
            }
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
        $dataView = [
            'title' => $title,
            'listDokter' => $listDokter,
        ];
        return Yii::$app->controller->render('index', compact('dataView'));
    }
}