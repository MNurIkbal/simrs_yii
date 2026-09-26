<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */

namespace app\modules\penatajasa\processes;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class FormTindakanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function getDataName($format){
        $data_name = [];
        if(!empty($format)){
            $tmpText = !empty($format['text']) ? explode(',', $format['text']) : [];
            $get['customSelect'] = $tmpText; // untuk diselect di backend untuk make sure apa yang sudah diconfigkan diselect di backend (pastikan kolomnya sudah ada di skema!)
            $tmpFormat = !empty($format['format']) ? explode(',', $format['format']) : [];
            $tmpTextFormated = [];
            if(!empty($tmpText)){
                foreach($tmpText as $k => $val){
                    $tmpTextFormated[$val] = trim(!empty($tmpFormat[$k]) ? $tmpFormat[$k] : 'text');
                }
            }
            $data_name = !empty($tmpTextFormated) ? $tmpTextFormated : $data_name;
        }
        return $data_name;
    }

    protected function processFlow($controller)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $jenis = !empty($get['jenis']) ? $get['jenis'] : '';
        // $api = ($get['jenis'] == 'tindakan') ? 'new-data-tindakan-ruangan' : 'new-data-paket-ruangan';
        $api = ($jenis == 'tindakan') ? 'get-data-tindakan' : 'get-data-paket';
        $url = 'informasi-pasien/'.$api;
        $data_id = ($jenis == 'tindakan') ? 'daftartindakan_id' : 'tipepaket_id';

        $initIniFile = @parse_ini_file('../config/env/.env', true);
        $formatTindakan = !empty($initIniFile['format_tindakan']) ? $initIniFile['format_tindakan'] : null;
        $formatPaket = !empty($initIniFile['format_paket']) ? $initIniFile['format_paket'] : null;

        if($jenis == 'tindakan') {
            $data_name = [                    
                'daftartindakan_kode',
                'daftartindakan_nama'
            ];
            if(!empty($formatTindakan)){
                $dataFormat = $this->getDataName($formatTindakan);
                $data_name = !empty($dataFormat) ? $dataFormat : $data_name;
            }
        }
        else {
            $data_name = [                    
                'tipepaket_kode',
                'tipepaket_nama'
            ];

            if(!empty($formatPaket)){
                $dataFormat = $this->getDataName($formatPaket);
                $data_name = !empty($dataFormat) ? $dataFormat : $data_name;
            }
        }
        
        $getRest = Yii::$app->docoRest->penatajasa;
        return DocoHelpers::paginationSelec2($url, $data_id, $data_name, $getRest, $get);
    }
}