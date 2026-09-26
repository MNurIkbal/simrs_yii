<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\IntPurchaseOrder;
use app\modules\v1\models\IntPurchaseOrderLine;
use app\modules\v1\models\IntReturnOrder;
use app\modules\v1\models\IntReturnOrderLine;

class ResyncAction extends Action {
    public function run($model) {
    	$request = Yii::$app->request;
    	$syncIds = $request->post('sync_id_api',[]);

    	if(!is_array($syncIds) && count($syncIds)<=0){
    		throw new \Exception("Undefined Payload", 1);
    	}

    	$updateIds = [];
    	switch ($model) {
            case 'purchaseorder':
                $dataview = IntPurchaseOrder::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'POS'){
                        $updateIds['grn'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'POM'){
                        $updateIds['grnpo'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'PBM'){
                        $updateIds['grnpobarang'][] = $_itemview['id'];
                    }
                }
                break;
            case 'purchaseorderline':
                $dataview = IntPurchaseOrderLine::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'POS'){
                        $updateIds['grn_detail'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'POM'){
                        $updateIds['grnpo_detail'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'PBM'){
                        $updateIds['grnpo_detailbarang'][] = $_itemview['id'];
                    }
                }
                break;
            case 'returnpurchaseorder':
                $dataview = IntReturnOrder::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'RPOS'){
                        $updateIds['retur_grn'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RPOM'){
                        $updateIds['retur_grnpo'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RPBM'){
                        $updateIds['retur_grnpobarang'][] = $_itemview['id'];
                    }
                }
                break;
            case 'returnpurchaseorderline':
                $dataview = IntReturnOrderLine::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'RPOS'){
                        $updateIds['retur_grn_detail'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RPOM'){
                        $updateIds['retur_grnpo_detail'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RPBM'){
                        $updateIds['retur_grnpo_detailbarang'][] = $_itemview['id'];
                    }
                }
                break;
    		
    		default:
    			throw new \Exception("Undefined Model", 1);
    			break;
    	}
        $tableName = $this->getTable();
        $result = 0;
        if(is_array($updateIds) && count($updateIds)>0){
            foreach ($updateIds as $tableKey => $ids) {
                if(isset($tableName[$tableKey]) && count($ids)>0){
                    $condition = [
                        'id' => $ids
                    ];
                    $attr = [
                        'is_sending' => false
                    ];
                    $table = $tableName[$tableKey];
                    $result = Yii::$app->db->createCommand()->update($table,$attr, $condition)->execute();
                }
            }
        }
        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA,[
            'data' => [
                'result' => $result
            ]
        ]);
    }

    private function getTable()
    {
        return [
            'grn' => 'penerimaansupp_r',
            'grnpo' => 'penerimaanobat_r',
            'grnpobarang' => 'penerimaanbarang_r',
            'grn_detail' => 'penerimaansuppdetail_r',
            'grnpo_detail' => 'penerimaanobatdetail_r',
            'grnpo_detailbarang' => 'penerimaanbarangdetail_r',
            'retur_grn' => 'returpenerimaanobat_r',
            'retur_grnpo' => 'returpenerimaanobat_r',
            'retur_grnpobarang' => 'returpenerimaanbarang_r',
            'retur_grn_detail' => 'returpenerimaanobatdetail_r',
            'retur_grnpo_detail' => 'returpenerimaanobatdetail_r',
            'retur_grnpo_detailbarang' => 'returpenerimaanbarangdetail_r'
        ];
    }
}