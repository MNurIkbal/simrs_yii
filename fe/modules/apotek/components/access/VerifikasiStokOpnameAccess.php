<?php

namespace Doco\apotek\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class VerifikasiStokOpnameAccess
{
	public function check($alreadyVerified)
	{
		try{
            if($alreadyVerified){
            	return FALSE;
            }

			$response = Yii::$app->docoRest->apotek->get('allow-konfig/is-verifikasi-stok-opname');
            $body = json_decode($response->getBody(), true);
            $is_verifikasi = $body['response'];

            if($is_verifikasi == FALSE){
            	return FALSE;
            }

			$akses = Yii::$app->session->get('akses_menu');
	        $link = '/apotek/informasi-stok-opname';
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
			$response = Yii::$app->docoRest->apotek->get('allow-konfig/is-verifikasi-stok-opname');
            $body = json_decode($response->getBody(), true);
            $is_verifikasi = $body['response'];

            return $is_verifikasi;
	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}

	public function verify($id)
	{
		try {
			$response = Yii::$app->docoRest->apotek->put('inf-stok-opname/verifikasi',['query'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
		}catch(RequestException $e){
            $error = json_decode($e->getResponse()->getBody(),true);
            $message = isset($error['response']['message']) ? $error['response']['message'] : $e->getMessage();
            return DocoHelpers::response([
            	'title' => 'Verifikasi Gagal',
            	'text' => $message,
            	'message' => $message,
            	'response'=>[
            		'title' => 'Verifikasi Gagal',
            		'text' => $message,
            		'message' => $message
            	]
            ], 422);
		} catch (\Exception $e) {
            return DocoHelpers::response(['message'=>$e->getMessage()], 500);
		}
	}
}