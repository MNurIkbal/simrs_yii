<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use app\modules\v1\models\StockOutView;
use app\modules\v1\models\StockOutDetailView;
use app\modules\v1\models\StockReturnView;
use app\modules\v1\models\StockReturnDetailView;
use app\modules\v1\models\StockScrapView;

class ResyncAction extends Action {
    public function run($model) {
    	$request = Yii::$app->request;
    	$syncIds = $request->post('sync_id_api',[]);

    	if(!is_array($syncIds) && count($syncIds)<=0){
    		throw new \Exception("Undefined Payload", 1);
    	}

    	$updateIds = [];
    	switch ($model) {
    		case 'stockout':
    			$dataview = StockOutView::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
    			foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'BMHP'){
                        $updateIds['stockout_bmhp'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RESEP'){
                        $updateIds['stockout_resep'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'RESEP_RACIKAN'){
                        $updateIds['stockout_resep_racikan'][] = $_itemview['id'];
                    }
    			}
    			break;
    		case 'stockoutdetail':
                $dataview = StockOutDetailView::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    $updateIds['stockout_detail'][] = $_itemview['id'];
                }
    			break;
    		case 'stockreturn':
                $dataview = StockReturnView::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'RETUR_RESEP'){
                        $updateIds['stockreturn_retur'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'BATAL_RESEP'){
                        $updateIds['stockreturn_batal'][] = $_itemview['id'];
                    }
                }
    			break;
    		case 'stockreturndetail':
                $dataview = StockReturnDetailView::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'RETUR_RESEP'){
                        $updateIds['stockreturndetail_retur'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'BATAL_RESEP'){
                        $updateIds['stockreturndetail_batal'][] = $_itemview['id'];
                    }
                }
    			break;
    		case 'storeconsumption':
                $dataview = StockScrapView::find(true)->where(['sync_id_api'=>$syncIds])->asArray()->all();
                foreach ($dataview as $_itemview) {
                    if($_itemview['tipe_rekap'] == 'adj_masuk'){
                        $updateIds['scrap_adj_masuk'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'adj_keluar'){
                        $updateIds['scrap_adj_keluar'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'pemusnahan_obat'){
                        $updateIds['scrap_pemusnahan'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'pemakaian_obat'){
                        $updateIds['scrap_pemakaian'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'stokopname_obat'){
                        $updateIds['scrap_opname'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'adj_masuk_barang'){
                        $updateIds['scrap_adj_masuk_barang'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'adj_keluar_barang'){
                        $updateIds['scrap_adj_keluar_barang'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'pemusnahan_barang'){
                        $updateIds['scrap_pemusnahan_barang'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'pemakaian_barang'){
                        $updateIds['scrap_pemakaian_barang'][] = $_itemview['id'];
                    }elseif($_itemview['tipe_rekap'] == 'stokopname_barang'){
                        $updateIds['scrap_opname_barang'][] = $_itemview['id'];
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

        return [
            'result' => $result
        ];
    }

    private function getTable()
    {
        return [
            'stockout_bmhp' => 'int_pendaftaranbmhp_r',
            'stockout_resep' => 'int_penjualanresep_r',
            'stockout_resep_racikan' => 'int_penjualanresep_r',
            'stockout_detail' => 'int_obatalkespasien_r',
            'stockreturn_retur' => 'returresep_r',
            'stockreturn_batal' => 'pembatalanresep_r',
            'stockreturndetail_retur' => 'returresepdetail_r',
            'stockreturndetail_batal' => 'pembatalanresepdetail_r',
            'scrap_adj_masuk' => 'adjusmenobatmasuk_r',
            'scrap_adj_keluar' => 'adjusmenobatkeluar_r',
            'scrap_pemusnahan' => 'pemusnahanobatdetail_r',
            'scrap_pemakaian' => 'pemakaianobatdetail_r',
            'scrap_opname' => 'stokopnamedetail_r',
            'scrap_adj_masuk_barang' => 'adjusmenbarangmasuk_r',
            'scrap_adj_keluar_barang' => 'adjusmenbarangkeluar_r',
            'scrap_pemusnahan_barang' => 'pemusnahanbarangdetail_r',
            'scrap_pemakaian_barang' => 'pemakaianbarangdetail_r',
            'scrap_opname_barang' => 'stokopnamebarangdetail_r'
        ];
    }
}