<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\apotek\components\access;

use Yii;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\Traits\ControllerHelperTrait;

class BatalPemesananAccess {
	use ControllerHelperTrait;

	protected $url_batal_pemesanan;

	public function check($status_pesan, $ruangan) {
		try{
			$resBatal = Yii::$app->docoRest->apotek->get('allow-konfig/batal-pesan-by');
            $bodyBatal = json_decode($resBatal->getBody(), true);
            $batal_pesan_by = $bodyBatal['response'];

            if($batal_pesan_by != $ruangan){
            	return FALSE;
            }

			if($ruangan == DocoConstants::BATAL_PESAN_PEMESAN) {
				$link = '/apotek/informasi-obat-alkes-keluar';

				if(($status_pesan != DocoConstants::BELUM_DIKIRIM)) {
					return false;
				}
			} else {
				$link = '/apotek/inf-pemesanan-obat-alkes';
				
				if($status_pesan != DocoConstants::BELUM_DIKIRIM) {
					return false;
				}
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
		return $this->guzzleExec(Yii::$app->docoRest->apotek, [
			'url' => $url,
			'method' => 'post',
			'payload' => [
				'json' => ['pesanobatalkes_id' => $id]
			],
			'returnResponse' => true
		]);
	}
}
