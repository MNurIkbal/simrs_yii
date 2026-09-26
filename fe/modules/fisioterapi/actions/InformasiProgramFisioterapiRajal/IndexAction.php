<?php

namespace Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal;

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
        $title = $helper->coalesce(DHtml::getTitleMenu(), Yii::t('fe', 'Program Fisioterapi'));
        $module = '/fisioterapi/informasi-program-fisioterapi-rajal/';
        $array_dokter = [];
        $status = [];
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-program-fisioterapi-rajal/get-attributes');
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            if ($response) {
                $dokterRujukan = ArrayHelper::getValue($response, 'dokter_perujuk', []);
                $statusProgram = ArrayHelper::getValue($response, 'status_program', []);
                $statusProgramOpenId = ArrayHelper::getValue($response, 'status_program_open');
            }
            if (is_array($dokterRujukan)) {
                $listDokter[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($dokterRujukan) > 0) {
                    foreach($dokterRujukan as $value) { 
                        $listDokter[] = [
                            'id'   => ArrayHelper::getValue($value, 'pegawai_id'),
                            'text' => ArrayHelper::getValue($value, 'nama_pegawai')
                        ]; 
                    }
                }   
            }
            if (is_array($statusProgram)) {
                $listStatus[] = [
                    'id'   => 'Semua',
                    'text' => 'Semua'
                ];
                if (count($statusProgram) > 0) {
                    foreach($statusProgram as $value) { 
                        $listStatus[] = [
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
            'listStatus' => $listStatus,
            'statusProgramOpenId' => $statusProgramOpenId
        ];
        return Yii::$app->controller->render('index', compact('dataView'));
    }
}
