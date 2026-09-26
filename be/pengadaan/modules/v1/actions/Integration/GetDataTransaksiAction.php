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
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\IntPurchaseOrder;
use app\modules\v1\models\IntPurchaseOrderLine;
use app\modules\v1\models\IntReturnOrder;
use app\modules\v1\models\IntReturnOrderLine;

class GetDataTransaksiAction extends Action {
    public function run($id,$model) {
    	try{
	    	switch ($model) {
	            case 'purchaseorder':
	                $dataview = IntPurchaseOrder::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
	                if(!empty($dataview['id'])){
		                if($dataview['tipe_rekap'] == 'POS' || $dataview['tipe_rekap'] == 'PBS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaansupp_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'POM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaanobat_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'PBM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaanbarang_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }
		            }
	                break;
	            case 'purchaseorderline':
	                $dataview = IntPurchaseOrderLine::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
	                if(!empty($dataview['id'])){
		                if($dataview['tipe_rekap'] == 'POS' || $dataview['tipe_rekap'] == 'PBS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaansuppdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'POM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaanobatdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'PBM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM penerimaanbarangdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }
		            }
	                break;
	            case 'returnpurchaseorder':
	                $dataview = IntReturnOrder::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
	                if(!empty($dataview['id'])){
		                if($dataview['tipe_rekap'] == 'RPOS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanobat_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPBS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanbarang_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPOM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanobatdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPBM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanbarangdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }
		            }
	                break;
	            case 'returnpurchaseorderline':
	                $dataview = IntReturnOrderLine::find(true)->where(['sync_id_api'=>$id])->asArray()->one();
	                if(!empty($dataview['id'])){
		                if($dataview['tipe_rekap'] == 'RPOS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanobatdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPOM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanobatdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPBS'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanbarangdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }elseif($dataview['tipe_rekap'] == 'RPBM'){
		                	$datasync = Yii::$app->db
			                    	->createCommand("SELECT id_sync_sercon,sync_respon FROM returpenerimaanbarangdetail_r where id = :id")
			                    	->bindValue(':id', $dataview['id'])
			                    	->queryOne();
		                }
		            }
	                break;

	    		default:
	    			throw new \Exception("Undefined Model", 1);
	    			break;
	    	}
	    	$payload = $result = null;
	    	if(!empty($datasync['sync_respon'])){
				$sync_respon = json_decode($datasync['sync_respon'],true);
				$payload = ArrayHelper::getValue($sync_respon,'data.payload',null);
				if(is_null($payload)){
					$payload = ArrayHelper::getValue($sync_respon,'payload',null);
				}
				$result = ArrayHelper::getValue($sync_respon,'data.response',null);
				if(is_null($result)){
					$result = ArrayHelper::getValue($sync_respon,'result',null);
				}
			}

			$process_uid = '';
			if(!empty($datasync['id_sync_sercon'])){
				$process_uid = ArrayHelper::getValue($datasync,'id_sync_sercon',null);
			}

			if(is_null($payload) && !empty($dataview)){
				$payload = $dataview;
			}

			return [
				'process_uid' => $process_uid,
				'payload' => $payload,
				'result' => $result
			];
		}catch(\Exception $e){
			return [
				'message' => $e->getMessage()
			];
		}
    }
}