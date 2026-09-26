<?php

namespace Doco\gudang\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class VerifikasiPemusnahanAccess
{
	public function check($is_verifikasi,$is_deleted)
	{
		try{
			if($is_deleted){
				return FALSE;
			}
			if($is_verifikasi){
				return FALSE;
			}

			$akses = Yii::$app->session->get('akses_menu');
	        $link = '/gudang/informasi-pemusnahan-obat';
	        $action = 'verifikasi-pemusnahan';
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
			$response = Yii::$app->docoRest->gudang->post('inf-pemusnahan-obat/verifikasi-pemusnahan',
                [
                    'query' => [
                        'pemusnahanobat_id' => DocoHelpers::decrypt($id)
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