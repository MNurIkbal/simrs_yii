<?php

namespace Doco\apotek\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class VerifikasiPemesananAccess
{
	public function check($status_pesan,$status_verifikasi)
	{
		try{
			$response = Yii::$app->docoRest->apotek->get('allow-konfig/is-verifikasi-pemesanan');
            $body = json_decode($response->getBody(), true);
            $is_verifikasi = $body['response'];

            if($is_verifikasi == FALSE){
            	return FALSE;
            }

			if(!($status_pesan == DocoConstants::BELUM_DIKIRIM && $status_verifikasi == DocoConstants::STATUS_VERIFIKASIOBAT_BELUM)){
				return FALSE;
			}

			$akses = Yii::$app->session->get('akses_menu');
	        $link = '/apotek/informasi-obat-alkes-keluar';
	        $action = 'verifikasi-pemesanan';
	        $hasAccess = isset($akses[$link]) ? $akses[$link] : [];
	        if(in_array($action, $hasAccess)){
	            return true;
	        }
	        return false;
	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}

	public function verify($id)
	{
		try {
			$response = Yii::$app->docoRest->apotek->get('inf-obat-alkes-keluar/verifikasi-pemesanan',['query'=>['pesanobatalkes_id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
		}catch(RequestException $e){
	    	return DocoHelpers::responseTemplate(500,'Gagal',[],['message'=>'Gagal','text'=>'Terjadi Kesalahan Internal']);
		} catch (\Exception $e) {
	    	return DocoHelpers::responseTemplate(500,'Gagal',[],['message'=>'Gagal','text'=>'Terjadi Kesalahan Internal']);
		}
	}

	public function checkKonfigOnly()
	{
		try{
			$response = Yii::$app->docoRest->apotek->get('allow-konfig/is-verifikasi-pemesanan');
            $body = json_decode($response->getBody(), true);
            $is_verifikasi = $body['response'];

            return $is_verifikasi;
	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}
}
