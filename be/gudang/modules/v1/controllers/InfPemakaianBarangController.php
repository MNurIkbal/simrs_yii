<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-03 11:00:21
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 09:33:09
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\models\InformasiPemakaianBarang;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\PemakaianBarang;
use app\modules\v1\models\PemakaianBarangDetail;
use app\modules\v1\models\StokBarang;

use app\components\GudangComponent;
class InfPemakaianBarangController extends DocoActiveController
{
	 public $modelClass = 'app\modules\v1\models\InformasiPemakaianBarang';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["print-detail"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
    	$model = new InformasiPemakaianBarang;
    	$query = $model::find();        
        $query->select(['no_pemakaianbarang','tgl_pemakaianbarang','pemakaianbarang_id','nama_pegawai']);
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pemakaianbarang'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tgl_pemakaianbarang']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                }
                unset($_GET['advanced-filter']['tgl_pemakaianbarang']); // Unset Advanced Filter  date range
                $between = true;
            }
        }                
        $query->andWhere(['between', 'tgl_pemakaianbarang', $start, $end]);                
        $query->groupBy(['no_pemakaianbarang','tgl_pemakaianbarang','pemakaianbarang_id','nama_pegawai']);

        if(isset($_GET['excel'])){
            return $query;
        }

    	$query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);        
    }

    public function actionGetNopemakaian()
    {
    	$request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataNopemakaian();        
        $result->select(['no_pemakaianbarang']);
        if(!empty($post['term'])){          
            $term = $post['term'];                     
            $result->where(['ILIKE','no_pemakaianbarang',$term]);
        }       
        $result->groupBy(['no_pemakaianbarang']);
        return $result->asArray()->all();
    }
    public function dataNopemakaian(){
        $data = InformasiPemakaianBarang::find();
        return $data;
    }
    public function actionDetailNopemakaian($id)
    {
    	// $result = $this->dataNopemakaian();
        $model = new InformasiPemakaianBarang;
        $result = $model::find();
        $result->andWhere(['pemakaianbarang_id'=>$id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }    
    public function actionGetHeader($id){
    	$result = $this->dataNopemakaian();
    	$result->select(['pemakaianbarang_id','tgl_pemakaianbarang','no_pemakaianbarang']);
    	$result->where(['=','pemakaianbarang_id',$id]);    	
    	return $result->asArray()->one();
    }
    public function actionSaveData()
    {
    	$connection = Yii::$app->db;
    	$transaction = $connection->beginTransaction();
    	try {
            $konfig = GudangComponent::getKonfig();            
    		$request = Yii::$app->request;
    		$post = $request->post();       		
    		$data = [];    		    		
    		$ruanganId = '';
    		$barangId = [];
    		$pemakaianbarang_id = '';
            $tanggalPemakaian = '';
            $noPemakaian = '';
    		if(count($post) > 0){
	    		foreach ($post as $key => $value) {	    			
	    			$newData = [];
	    			if(!empty($post[$key]['barang_id'])){
	    				$barangId[] =$post[$key]['barang_id'];
	    			}
	    			if(!empty($post[$key]['pemakaianbarang_id'])){
	    				$pemakaianbarang_id = $post[$key]['pemakaianbarang_id'];
	    			}	    			
	    			if(!empty($post[$key]['ruangan_id'])){
	    				$ruanganId = $post[$key]['ruangan_id'];
	    			}
	    			$newData['pemakaianbarang_id'] = !empty($post[$key]['pemakaianbarang_id']) ? $post[$key]['pemakaianbarang_id'] : '';
	    			$newData['barang_id'] = !empty($post[$key]['barang_id']) ? $post[$key]['barang_id'] : '';
	    			$newData['jumlah_pakai'] = !empty($post[$key]['jumlah_pakai']) ? $post[$key]['jumlah_pakai'] : '';
	    			$newData['jumlah_input'] = !empty($post[$key]['jumlah_input']) ? $post[$key]['jumlah_input'] : '';
	    			$newData['harga_netto'] = !empty($post[$key]['harga_netto']) ? $post[$key]['harga_netto'] : '';
	    			$newData['ppn'] = !empty($post[$key]['ppn']) ? $post[$key]['ppn'] : 10 ;
	    			$newData['disc'] = !empty($post[$key]['disc']) ? $post[$key]['disc'] : 10;
	    			$newData['hpp'] = !empty($post[$key]['hpp']) ? $post[$key]['hpp'] : 10;
	    			$newData['harga_jual'] = !empty($post[$key]['hargajual']) ? $post[$key]['harga_jual'] : 0;
	    			$newData['catatan_barang'] = !empty($post[$key]['keteranganpakai']) ? $post[$key]['keteranganpakai'] : '';
	    			$newData['satuanbesar_id'] = !empty($post[$key]['satuanbesar_id']) ? $post[$key]['satuanbesar_id'] : '';
	    			$newData['satuankecil_id'] = !empty($post[$key]['satuankecil_id']) ? $post[$key]['satuankecil_id'] : '';
	    			$data[] = $newData;
	    		}	    	    			    	
	    		$arrKey = array_keys(current($data));	    			    		
	    		$barangId = implode(',', $barangId);
	    		$save_detail = $connection->createCommand()->batchInsert('pemakaianbarangdetail_t',$arrKey, $data)->execute();	    		
	    		if($save_detail){	    			
	    			$get_detail = $connection->createCommand("select * from pemakaianbarangdetail_t where pemakaianbarang_id = {$pemakaianbarang_id}")->queryAll();

                    if($konfig == 'LIFO'){
                        $methode = GudangComponent::methodeLIFO($get_detail, date('Y-m-d'), $ruanganId);
                    }else{
                        $methode = GudangComponent::methodeFIFO($get_detail, date('Y-m-d'), $ruanganId);                        
                    }	 
                    $transaction->commit();    
                    return [
                        'message' => 'sukses',
                        'id_parent' => $pemakaianbarang_id,                        
                    ];         			
	    		}    			    	

    		}	    		
    		return false;
    		
    	} catch (\yii\db\Exception $e) {
    		$transaction->rollBack();    		
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
        	$transaction->rollBack();    		
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
    * @controller actionPrintDetail
    * @attribute #data_barang# => print 

    **/

    public function actionPrintDetail($id)    
    {
        $id = $_GET['id'];            
        $model = new InformasiPemakaianBarang;
        $query = $model::find();        
        $query->where(['pemakaianbarang_id'=>$id]);
        $data_detail = [];                
        $data_barang = [];

        foreach ($query->asArray()->all() as $key => $value) {
            if(!isset($data_detail['ruangan_nama'])){
                $data_detail['ruangan_nama'] = $value['ruangan_nama'];                
            }
            if(!isset($data_detail['no_pemakaianbarang'])){
                $data_detail['no_pemakaianbarang'] = $value['no_pemakaianbarang'];
            }
            if(!isset($data_detail['tgl_pemakaianbarang'])){
                $data_detail['tgl_pemakaianbarang'] = date("d M Y H:i:s", strtotime($value['tgl_pemakaianbarang']));
            }
            $newData = [];
            $newData['barang_nama'] = $value['barang_nama'];
            $newData['qty_besar']  = $value['jumlah_input'];
            $newData['satuan_besar']= $value['satuan_besar'];
            $newData['qty_kecil']  = $value['jumlah_pakai'];
            $newData['satuan_kecil']= $value['satuan_kecil'];
            $newData['catatan_barang'] = $value['catatan_barang'];
            $data_barang[] = $newData;
        }
        $result = ['detail'=>$data_detail, 'data_barang'=>$data_barang];
        $print = new DocoPrint();    
        $print->attributes = [
            '#data_barang#' => $this->renderPartial('index',$result
        ),
        ];
        $print->Output();
    }       
    public function actionDeleteDetail($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {            
            $select_detail = $connection->createCommand("select pemakaianbarangdetail_id from pemakaianbarangdetail_t where pemakaianbarang_id = {$id}")->queryAll();
            $detailId = [];
            foreach ($select_detail as $key => $value) {
                $detailId[] = $value['pemakaianbarangdetail_id'];
            }                                
            $delete_header = (new PemakaianBarang)->delete($id);
            $delete_child = (new PemakaianBarangDetail)->delete(['pemakaianbarang_id'=>$id]);
            $delete_stok = (new StokBarang)->delete(['pemakaianbarangdetail_id'=>$detailId]);

            $transaction->commit();
            return ['message'=>'done'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();           
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();           
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
