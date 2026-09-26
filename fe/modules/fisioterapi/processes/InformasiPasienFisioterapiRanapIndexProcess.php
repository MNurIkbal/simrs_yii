<?php

/**
 * @author : Ardi Pratama (ardi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\fisioterapi\processes;

use Yii;
use app\components\DHtml;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class InformasiPasienFisioterapiRanapIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected $_title = 'Pasien Fisioterapi';
    protected $_module = '/fisioterapi/informasi-pasien-fisioterapi-ranap/';
    protected $allowAction = ['*'];

    protected function processFlow($controller)
    {
        $title = (new DocoHelpers)->coalesce(DHtml::getTitleMenu(), $this->_title);
        $dokterRujukan = [];
        $status = [];
        $list_dokter = [];
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/get-attributes');
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            if($response){
                $dokterRujukan = ArrayHelper::getValue($response, 'dokterPerujuk', []);
                $statusPeriksaRanap = ArrayHelper::getValue($response, 'statusPeriksaRanap', []);
                $caraBayar = ArrayHelper::getValue($response, 'caraBayar', []);
                $statusBayarRanap = ArrayHelper::getValue($response, 'getStatusBayar', []);
                $jenisTerapi = ArrayHelper::getValue($response, 'jenisTerapi', []);
            }
            if (is_array($dokterRujukan)) {
                $listDokter[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if(count($dokterRujukan) > 0) {
                    foreach($dokterRujukan as $value) { 
                        $listDokter[] = [
                            'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ]; 
                    }
                }   
            }
            if (is_array($statusPeriksaRanap)) {
                $listStatusPeriksaRanap[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if(count($statusPeriksaRanap) > 0) {
                    foreach($statusPeriksaRanap as $value) {
                        $listStatusPeriksaRanap[] = [
                            'id'   => ArrayHelper::getValue($value, 'lookup_id'),
                            'text' => ArrayHelper::getValue($value, 'lookup_name')
                        ];
                    }
                }
            }
            if (is_array($caraBayar)) {
                $listCaraBayar[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if(count($caraBayar) > 0) {
                    foreach($caraBayar as $value) {
                        $listCaraBayar[] = [
                            'id'   => ArrayHelper::getValue($value, 'carabayar_id'),
                            'text' => ArrayHelper::getValue($value, 'carabayar_nama')
                        ];
                    }
                }
            }
            if (is_array($statusBayarRanap)) {
                $listStatusBayarRanap[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if(count($statusBayarRanap) > 0) {
                    foreach($statusBayarRanap as $value) {
                        $listStatusBayarRanap[] = [
                            'id'   => strtoupper(ArrayHelper::getValue($value, 'lookup_name')),
                            'text' => strtoupper(ArrayHelper::getValue($value, 'lookup_name'))
                        ];
                    }
                }
            }
            if (is_array($jenisTerapi)) {
                $listjenisTerapi[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if(count($jenisTerapi) > 0) {
                    foreach($jenisTerapi as $value) {
                        $listjenisTerapi[] = [
                            'id'   => strtoupper(ArrayHelper::getValue($value, 'jenispemeriksaanfisio_id')),
                            'text' => strtoupper(ArrayHelper::getValue($value, 'jenispemeriksaanfisio_nama'))
                        ];
                    }
                }
            }
            $verifyButton = DocoHelpers::checkButtonAccess('/fisioterapi/informasi-pasien-fisioterapi', 'get-history-fisioterapi');
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } 
        return $controller->render('index', get_defined_vars());
    }
}
