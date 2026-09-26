<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\IntegrationOdoo;

use Yii;
use yii\base\Action;

class CallbackCreateAction extends Action {

	public $modelClassRekap = '';

    public function run() {
    	$request = Yii::$app->request;

        $uid = $request->post('uid',null);
        $isError = $request->post('is_error',null);
        $data = $request->post('data',null);

        $modelRekap = new $this->modelClassRekap;
        if($isError){
        	$payload = $request->post('error_payload',null);
        	if(isset($payload['id'])){
        		$rekap = $modelRekap::find()->where(['id'=>$payload['id']])->one();

        		$rekap->id_sync_sercon = $uid;
        		$rekap->is_sending = false;
        		$currentData = $this->extractJson($rekap->sync_respon);
        		if(is_null($currentData)){
        			$currentData = [];
        		}
        		$currentData[date('YmdHis')] = $request->post();
        		$rekap->sync_respon = json_encode($currentData);
        		$rekap->save();

        		$this->controller->logWarning(['message'=>'error callback create','payload'=>$request->post()]);

        		return true;
        	}
        }else{
        	$payload = $data['payload'];

        	if(isset($payload['id'])){
        		$rekap = $modelRekap::find(true)->where(['id'=>$payload['id']])->one();

        		$rekap->id_sync_sercon = $uid;
        		$rekap->is_sent = true;
        		$rekap->is_sending = true;
        		$currentData = $this->extractJson($rekap->sync_respon);
        		if(is_null($currentData)){
        			$currentData = [];
        		}
        		$currentData[date('YmdHis')] = $request->post();
        		$rekap->sync_respon = json_encode($currentData);
        		$rekap->save();

        		$this->controller->logWarning(['message'=>'success callback create','payload'=>$request->post()]);

        		return true;
        	}
        }

        throw new \Exception("Callback Create Failed", 1);
        
    }

    protected function extractJson($string)
    {
		$result = json_decode($string,TRUE);
		switch(json_last_error()) {
		    case JSON_ERROR_DEPTH:
		        // echo ' - Maximum stack depth exceeded';
		    	$is_error = true;
		    	break;
		    case JSON_ERROR_CTRL_CHAR:
		        // echo ' - Unexpected control character found';
		    	$is_error = true;
		    	break;
		    case JSON_ERROR_SYNTAX:
		        // echo ' - Syntax error, malformed JSON';
		    	$is_error = true;
		    	break;
		    case JSON_ERROR_NONE:
		    	$is_error = false;
		    break;
		}

		return ($is_error == false) ? $result : $string;
    }
}