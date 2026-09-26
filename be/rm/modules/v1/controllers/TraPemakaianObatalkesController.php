<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-23 13:52:27
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-24 09:53:34
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;

class TraPemakaianObatalkesController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PemakaianObatalkes';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["create"] = ["POST"];
        $verbs["list-obat"] = ["GET"];
        $verbs["list-satuan"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();        
        unset($actions['create']);
        return $actions;
    }
    public function actionCreate(){
    	try {
    		$request = Yii::$app->request;
            $post = $request->post();            
            $tgl_pemakaian = $post['tgl_pemakaian'];
            $isi_data = json_decode($post['isi_data'], true);
            $pegawai_id = $post['pegawai_id'];
            $ruangan_id = $post['ruangan_id'];
            $nopemakaian_obat = 88;
            $untukkeperluan_obat = "tester";        
            
           	$sql = "INSERT INTO pemakaianobat_t (pegawai_id,ruangan_id,tglpemakaianobat,nopemakaian_obat,untukkeperluan_obat,created_date,is_deleted,is_active) 
           		values ('{$pegawai_id}','{$ruangan_id}','{$tgl_pemakaian}','{$nopemakaian_obat}','{$untukkeperluan_obat}','{$tgl_pemakaian}',false,true)";
            $create_pemakaianobat = \Yii::$app->db->createCommand($sql)->execute();            

            if($create_pemakaianobat){            	
            	$pemakaianobat_id = \Yii::$app->db->getLastInsertID();
            	$tabledata = [];
            	$tablename = "pemakaianobatdetail_t";
            	$tablerow = [
            		'pemakaianobat_id',
            		'satuankecil_id',
            		'obatalkes_id',
            		'qty_satuanpakai',
            		'harga_satuanpakai',
            		'harganetto_satuanpakai',
            		'created_date',
            		'created_by',
            		'last_modified_date',
            		'is_deleted',
            		'is_active'
            	];
            	foreach ($isi_data as $key => $value) {
            		$tabledata[] = [
            			'pemakaianobat_id'=>$pemakaianobat_id,
            			'satuankecil_id'=>$value['satuanid'],
            			'obatalkes_id'=>$value['obatid'],
            			'qty_satuanpakai'=>$value['qty'],
            			'harga_satuanpakai'=>5000,
            			'harganetto_satuanpakai'=>5000,
            			'created_date'=>date('Y-m-d'),
            			'created_by'=>$pegawai_id,
            			'last_modified_date'=>date('Y-m-d'),
            			'is_deleted'=>false,
            			'is_active'=>true,
            		];
            	}
            	
            	$insert_detail = \Yii::$app->db->createCommand()
            								   ->batchInsert($tablename,$tablerow,$tabledata)
            								   ->execute();
            	if($insert_detail){
            		return [
                                'message' => 'Data Berhasil di simpan',
                            ];
            	}
            }else{
            	return "error";
            }
            //return $ruangan_id;
    	} catch (\yii\db\Exception $e) {
    		\Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
    	} catch (\Exception $e) {
    		\Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
    	}
    }
    public function actionListData()
    {
    	try {
    		$data_obat = $this->getObat();
	    	$data_satuan = $this->getSatuan();
	    	$return = [
	    		'data_obat'=>$data_obat,
	    		'data_satuan'=>$data_satuan,
	    	];
	    	return $return;
    	} catch (\yii\db\Exception $e) {
    		\Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
    	} catch (\Exception $e) {
    		\Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
    	}

    	
    }

    public function getObat()
    {	
    	$sql = "SELECT obatalkes_id, obatalkes_namalain,hargajual,harganetto FROM obatalkes_m WHERE is_deleted = false";
    	$data = \Yii::$app->db->createCommand($sql)->queryAll();    	
    	return $data;
    }
    public function getSatuan()
    {
    	$sql = "SELECT satuankecil_id, satuankecil_nama FROM satuankecil_m WHERE is_deleted = false";
    	$data = \Yii::$app->db->createCommand($sql)->queryAll();    	
    	return $data;
    }
}