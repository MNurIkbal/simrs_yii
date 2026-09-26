<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiMutasi;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\apotek\services\InfMutasiObatalkesService;

class DetailPenerimaanAction extends Action {
    protected $_title = "";

    public function run($nomutasioa)
    {
    	$request = Yii::$app->request;

    	$response = InfMutasiObatalkesService::getDetail([
    		'nomutasioa' => $nomutasioa
    	]);

        $i = 1;
        foreach ($response['detail'] as $k => $detail) {
            $response['detail'][$k]['rowNum'] = $i;
            $response['detail'][$k]['konv'] = 0;
            $jum_mutasi = ArrayHelper::getValue($detail, 'jumlah_mutasi', 0);
            $response['detail'][$k]['expired'] = isset($detail['expired']) ? date('d F Y',strtotime($detail['expired'])) : '-';
            
            if(empty($detail['jumlah_input']) || is_null($detail['jumlah_input']) || $detail['jumlah_input'] == 0){    
                $jum_input = ArrayHelper::getColumn($detail, 'qty_besar', 0);
            }else{
                $jum_input = ArrayHelper::getColumn($detail, 'jumlah_input', 0);
            }

            $detail['jumlah_input'] = DocoHelpers::formatNumber($jum_input);

            $response['detail'][$k]['jumlah_mutasi'] = DocoHelpers::formatNumber($jum_mutasi);
            $response['detail'][$k]['jumlah_input'] = ArrayHelper::getColumn($detail, 'jumlah_input', 0);
            $response['detail'][$k]['satuanbesar_nama'] = isset($detail['satuanbesar_nama']) ? $detail['satuanbesar_nama'] : $detail['satuan_mutasi'];
            $i++;
        }

        $result['data'] = $response['detail'];
        $result['draw'] = $request->post('draw');
        $result['recordsTotal'] = count($response['detail']);
        $result['recordsFiltered'] = count($response['detail']);
        return DocoHelpers::response($result);
    }
}
