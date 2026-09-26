<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal;

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
        $listDokter = [];
        $status = [];
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('laporan-pasien-fisioterapi-rajal/get-attributes');
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            if ($response) {
                $dokterPerujuk = ArrayHelper::getValue($response, 'dokter_perujuk', []);
                $dokterDpjp = ArrayHelper::getValue($response, 'dokter_dpjp', []);
                $statusPeriksa = ArrayHelper::getValue($response, 'status_periksa', []);
            }
            if (is_array($dokterPerujuk)) {
                $listDokter[] = [
                    'id' => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($dokterPerujuk) > 0) {
                    foreach($dokterPerujuk as $value) { 
                        $listDokter[] = [
                            'id' => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ];
                    }
                }   
            }
            if (is_array($dokterDpjp)) {
                $listDokterDpjp[] = [
                    'id' => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($dokterDpjp) > 0) {
                    foreach($dokterDpjp as $value) { 
                        $listDokterDpjp[] = [
                            'id' => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ];
                    }
                }   
            }
            if (is_array($statusPeriksa)) {
                $listStatusPeriksa[] = [
                    'id' => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($statusPeriksa) > 0) {
                    foreach($statusPeriksa as $value) { 
                        $listStatusPeriksa[] = [
                            'id'   => ArrayHelper::getValue($value, 'lookup_id'),
                            'text' => ArrayHelper::getValue($value, 'lookup_value')
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
            'listDokterDpjp' => $listDokterDpjp,
            'listStatusPeriksa' => $listStatusPeriksa,
        ];
        return Yii::$app->controller->render('index', compact('dataView'));    
    }
}