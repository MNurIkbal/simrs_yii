<?php

namespace Doco\gudang\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class VerifikasiStokOpnameAccess
{
	public function check($alreadyVerified)
	{
		try{
            if($alreadyVerified){
            	return FALSE;
            }

			$is_verifikasi = $this->checkKonfigOnly();
            if($is_verifikasi == FALSE){
            	return FALSE;
            }

			$akses = Yii::$app->session->get('akses_menu');
	        $link = '/gudang/informasi-formulir-so-barang';
	        $action = 'verifikasi';
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

	public function checkKonfigOnly()
	{
		try{
			$response = Yii::$app->docoRest->master->get('master-api/get-konfig-gudang');
            $body = json_decode($response->getBody(), true);
            $is_verifikasi = ArrayHelper::getValue($body['response'], 'is_verifstokopnamebarang', false);
            return $is_verifikasi;
	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}
}