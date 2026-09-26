<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\components\access;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\Traits\ControllerHelperTrait;

class BatalPemesananBarangAccess {
	use ControllerHelperTrait;

	public function check($status_pesan, $ruangan) {
		try{
			$response = Yii::$app->docoRest->apotek->get('allow-konfig/batal-pesan-by');
            $body = json_decode($response->getBody(), true);
            $batal_pesan_by = $body['response'];
			
            if($batal_pesan_by != $ruangan){
            	return FALSE;
            }

			if(!($status_pesan == DocoConstants::BELUM_DIKIRIM)){
				return FALSE;
			}

			if($ruangan == DocoConstants::BATAL_PESAN_PEMESAN) {
				$link = '/gudang/informasi-pemesanan-barang-keluar';
			} else {
				$link = '/gudang/inf-pemesanan-barang';
			}
			
			$action = 'batal-pemesanan';
			$akses = Yii::$app->session->get('akses_menu');
	        $hasAccess = isset($akses[$link]) ? $akses[$link] : [];
	        if(in_array($action, $hasAccess)){
	            return true;
	        }
	        return false;
	    } catch(RequestException $e){
	    	return false;
        } catch(\Exception $e){
        	return false;
        }
	}

	public function batal($id, $url) {
		return $this->guzzleExec(Yii::$app->docoRest->gudang, [
			'url' => $url,
			'method' => 'post',
			'payload' => [
				'json' => ['pesanbarang_id' => $id]
			],
			'returnResponse' => true
		]);
	}
}
