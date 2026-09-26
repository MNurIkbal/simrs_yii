<?php

namespace Doco\pengadaan\components\access;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class PurchaseOrderValidasiAccess
{
	public function check($model)
	{
		try{
			$akses = Yii::$app->session->get('akses_menu');
	        $link = '/pengadaan/info-purchase-order';
	        $action = 'validasi';
	        $hasAccess = isset($akses[$link]) ? $akses[$link] : [];
	        if(in_array($action, $hasAccess)){
				$isStatusPenerimaan = $model->status_penerimaan == DocoConstants::STATUS_BELUM_TERIMA_PO
				? !empty($model->is_validasi) ? true : false
				: true;
	
				return !$isStatusPenerimaan;
	        }
			return false;

	    }catch(RequestException $e){
	    	return false;
        }catch(\Exception $e){
        	return false;
        }
	}
}