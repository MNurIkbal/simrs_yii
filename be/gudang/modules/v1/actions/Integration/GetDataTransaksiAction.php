<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\StockOutView;
use app\modules\v1\models\StockOutDetailView;
use app\modules\v1\models\StockReturnView;
use app\modules\v1\models\StockReturnDetailView;
use app\modules\v1\models\StockScrapView;

class GetDataTransaksiAction extends Action {
    public function run($id,$model) {
    	switch ($model) {
    		case 'stockout':
    			$dataview = StockOutView::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
                $datasync = $dataview;
    			break;
    		case 'stockoutdetail':
                $dataview = StockOutDetailView::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
                $datasync = $dataview;
    			break;
    		case 'stockreturn':
                $dataview = StockReturnView::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
                if(!empty($dataview['id'])){
	                if($dataview['tipe_rekap'] == 'RETUR_RESEP'){
	                    $datasync = Yii::$app->db
	                    	->createCommand("SELECT sync_respon FROM returresep_r where id = :id")
	                    	->bindValue(':id', $dataview['id'])
	                    	->queryOne();
	                }elseif($dataview['tipe_rekap'] == 'BATAL_RESEP'){
	                	$datasync = Yii::$app->db
	                    	->createCommand("SELECT sync_respon FROM pembatalanresep_r where id = :id")
	                    	->bindValue(':id', $dataview['id'])
	                    	->queryOne();
	                }
	            }
    			break;
    		case 'stockreturndetail':
                $dataview = StockReturnDetailView::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
                if(!empty($dataview['id'])){
	                if($dataview['tipe_rekap'] == 'RETUR_RESEP'){
	                    $datasync = Yii::$app->db
	                    	->createCommand("SELECT sync_respon FROM returresepdetail_r where id = :id")
	                    	->bindValue(':id', $dataview['id'])
	                    	->queryOne();
	                }elseif($dataview['tipe_rekap'] == 'BATAL_RESEP'){
	                	$datasync = Yii::$app->db
	                    	->createCommand("SELECT sync_respon FROM pembatalanresepdetail_r where id = :id")
	                    	->bindValue(':id', $dataview['id'])
	                    	->queryOne();
	                }
	            }
    			break;
    		case 'storeconsumption':
                $dataview = StockScrapView::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
                $datasync = $dataview;
    			break;

    		default:
    			throw new \Exception("Undefined Model", 1);
    			break;
    	}

    	$payload = $result = $uid = null;
    	if(!empty($datasync['sync_respon'])){
			$sync_respon = json_decode($dataview['sync_respon'],true);
            $uid = ArrayHelper::getValue($sync_respon,'uid',null);
			$payload = ArrayHelper::getValue($sync_respon, 'payload', null);
			$result = ArrayHelper::getValue($sync_respon, 'result', null);
		}

		if(is_null($payload) && !empty($dataview)){
			$payload = $dataview;
		}

		return [
            'uid' => $uid,
			'payload' => $payload,
			'result' => $result
		];
    }
}