<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GeneratePOAction extends Action {
    public function run() {
    	$pr = Yii::$app->request->post('pr','');
    	if(is_null($pr)) throw new \Exception("Parameter Tidak Ditemukan", 1);

    	$pr = json_decode($pr,true);
    	$listPr = is_array($pr) ? array_column($pr, 'no_pr') : [];
    	$type = is_array($pr) ? array_column($pr, 'tipe') : [];
    	$type = array_unique($type);

    	try{
	    	$response = Yii::$app->docoRest->pengadaan->get('generate-po/by-pr-multiple', [
	                'form_params' => [
	                	'listPr' => $listPr,
	                	'type' => $type
	                ]
	            ]);
	        $body = json_decode($response->getBody(), true);

			return DocoHelpers::response($body);
	    }catch(RequestException $e){
	    	$body = json_decode($e->getResponse()->getBody(), true);
	    	return DocoHelpers::response($body,500);
	    }catch(\Exception $e){
	    	return DocoHelpers::response([],500);
	    }
    }
}
