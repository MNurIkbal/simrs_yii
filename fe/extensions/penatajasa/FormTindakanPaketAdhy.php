<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */

namespace app\extensions\penatajasa;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use GuzzleHttp\Psr7\Response;
use \yii\base\Model;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;

class FormTindakanPaketAdhy extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
        $request = Yii::$app->request;
        $get = $request->get();
        $api = ($get['jenis'] == 'tindakan') ? 'new-data-tindakan-ruangan-adhy' : 'new-data-paket-ruangan-adhy';
        $url = 'informasi-pasien/'.$api;
        $data_id = ($get['jenis'] == 'tindakan') ? 'daftartindakan_id' : 'tipepaket_id';
        if($get['jenis'] == 'tindakan') {
            $data_name = [                    
                'daftartindakan_kode',
                'daftartindakan_nama',
                'harga_tariftindakan'
            ];
        }
        else {
            $data_name = [                    
                'tipepaket_kode',
                'tipepaket_nama',
                'harga_tariftindakan'
            ];
        }
        $get['dokter_id'] = ($get['dokter_id'] == 'Loading ...') ? null:$get['dokter_id'];
        $getRest = Yii::$app->docoRest->penatajasa;
        return DocoHelpers::paginationSelec2($url, $data_id, $data_name, $getRest, $get);
	}
}