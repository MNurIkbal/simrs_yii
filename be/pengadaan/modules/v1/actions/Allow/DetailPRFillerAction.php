<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2021-02-25 17:30:00
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseRequisitionGabung;
use app\modules\v1\models\InfoPurchaseReqGabungDetailView;
use Doco\components\DocoRestActiveFilter;

class DetailPRFillerAction extends Action {
    public function run($type, $id) {
    	try{
            $header = InfoPurchaseRequisitionGabung::find()
                    ->where(['purchasereq_id'=>$id, 'tipe' => $type])
                    ->asArray()->one();
            
            if(is_null($header))
                throw new \Exception("Data dengan id:{$id} tidak ditemukan", 1);
                
            $detail = InfoPurchaseReqGabungDetailView::find()
                        ->where(['purchasereq_id'=>$id, 'tipe' => $type])
                        ->orderBy(['item_nama' => SORT_ASC])
                        ->asArray()->all();

    		return [
    			'data' => [
    				'header' => $header,
    				'detail' => $detail
    			]
    		];
    	}catch(\Exception $e){
    		return [
    			'message' => $e->getMessage()
    		];
    	}
    }
}
