<?php

namespace Doco\apotek\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class FormulirStokOpnameAccess
{
	public function check()
	{
        //get-info-detail
        //delete-formulir
		try{
			$akses = Yii::$app->session->get('akses_menu');
            
	        $link = '/apotek/informasi-stok-opname';
            $action1 = 'get-info-detail';
	        $action2 = 'delete-formulir';
	        $hasAccess = isset($akses[$link]) ? $akses[$link] : [];
	        if(in_array($action1, $hasAccess) || in_array($action2, $hasAccess)){
	            return true;
	        }
	        return false;
	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}
	
}