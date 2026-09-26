<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\services;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfPemusnahanObatService {

	public static function postBatalPemusnahan($pemusnahanobat_id){
		try {
			$response = Yii::$app->docoRest->gudang->post('inf-pemusnahan-obat/batal-pemusnahan',
                [
                    'query' => [
                        'pemusnahanobat_id' => $pemusnahanobat_id
                    ],
                    'form_params' => [
                        'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id")
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
		}catch(RequestException $e){
	    	return DocoHelpers::responseTemplate(500,'Gagal',[],['message'=>'Gagal','text'=>'Terjadi Kesalahan Internal']);
		} catch (\Exception $e) {
	    	return DocoHelpers::responseTemplate(500,'Gagal',[],['message'=>'Gagal','text'=>'Terjadi Kesalahan Internal']);
		}
	}
}