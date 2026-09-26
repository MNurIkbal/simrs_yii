<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-06 15:02:15
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-12 09:32:10
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\InfoFormulirStokOpnameView;

class StokOpnameController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoReturResepView';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["verifikasi"] = ["PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }


    /*
    *last edited by: Rizqi Fitrianto
    *add create stok opname function
    *done programmer 12/02/2018
    */
    public function actionCreate()
    {
    	$request = Yii::$app->request;
    	$post = $request->post();
    	$post = $post['StokForm'];
    	$count = count($post['obatalkes_id']);
    	$data = [];
        $data2 = [];
    	$stokopname_nomor = $this->getStokOpnameNomor(5);
    	$data_stokopname = [
				    		'ruangan_id'=>$post['ruangan_id'],
				    		'formuliropname_id'=>$post['formuliropname_id'],
				    		'tglstokopname'=>date('Y-m-d H:i:s'),
				    		'nostokopname'=>$stokopname_nomor,
				    		'jenisstokopname'=>$post['jenis_stokopname'],
				    		'totalharga_fisik'=>$post['total_harganetto_fisik'],
				    		'totalharga_sistem'=>$post['total_harganetto_sistem'],
				    		'mengetahui_id'=>$post['pegawai_id'],
				    		'petugas1_id'=>$post['pegawai_id'],
				    		'created_date'=>date('Y-m-d'),
							'created_by'=>$post['pegawai_id'],
							'last_modified_date'=>date('Y-m-d'),
							'is_deleted'=>0,
							'is_active'=>1,
							'deleted_date'=>date('Y-m-d'),
				    	];
		$insert = Yii::$app->db->createCommand()->insert('stokopname_t',$data_stokopname)->execute();
		$stokopname_id = Yii::$app->db->getLastInsertID();
    	$data_obatalkes = [];
        $data_stokobatalkes = [];
        $listfalse = [];

    	for($i = 1;$i <= $count; $i++){
            $date = date('Y-m-d',strtotime($post['tgl_kadaluarsa'][$i]));
            $min = date('H:i:s');
            $date = $date.' '.$min;
    		$data['formstokopname_id'] = $post['formuliropname_id'];
    		$data['obatalkes_id'] = $post['obatalkes_id'][$i];
    		$data['volume_fisik'] = $post['stok_fisik'][$i];
    		$data['volume_sistem'] = $post['stok_sistem'][$i];
    		$data['hargasatuan'] = $post['harga_satuan'][$i];
    		$data['kondisibarang'] = $post['kondisi'][$i];
    		$data['tglkadaluarsa'] = $date;
    		$data['tglperiksafisik'] = date('Y-m-d H:i:s');
    		$data['stokopname_id'] = $stokopname_id;
    		$data['created_date'] = date('Y-m-d');
    		$data['created_by'] = $post['pegawai_id'];
    		$data['last_modified_date'] = date('Y-m-d');
    		$data['is_deleted'] = false;
    		$data['is_active'] = true;
    		$data['deleted_date'] = date('Y-m-d');

            $data2['ruangan_id'] = $post['ruangan_id'];
            $data2['obatalkes_id'] = $post['obatalkes_id'][$i];
            $data2['tglkadaluarsa'] = $date;
            $data2['nobatch'] = $post['nobatch'][$i];
            $hasil = $post['stok_sistem'][$i]-$post['stok_fisik'][$i];

            if($hasil > 0){
                $data2['qtystok_in'] = 0;
                $data2['qtystok_out'] = abs($hasil);
                $data2['tglstok_in'] = null;
                $data2['tglstok_out'] = date('Y-m-d H:i:s');
            }else if($hasil < 0){
                $data2['qtystok_in'] = abs($hasil);
                $data2['qtystok_out'] = 0;
                $data2['tglstok_out'] = null;
                $data2['tglstok_in'] = date('Y-m-d H:i:s');
            }else if($hasil == 0){
                $data2['qtystok_in'] = 0;
                $data2['qtystok_out'] = $post['stok_fisik'][$i];
                $data2['tglstok_in'] = null;
                $data2['tglstok_out'] = date('Y-m-d H:i:s');
                $listfalse[] = $post['obatalkes_id'][$i];
            }
            $data2['stokoa_aktif'] = true;

    		$data_obatalkes[$post['obatalkes_id'][$i]] = $data;
            $data_stokobatalkes[$post['obatalkes_id'][$i]] = $data2;
    	}
        $obatalkes = implode(',', $post['obatalkes_id']);
        $listfalse = implode(',', $listfalse);
        // return $listfalse;
        // if(strtolower($post['jenis_stokopname']) != 'penyesuaian'){
        //     $sql = "update stokobatalkes_t set stokoa_aktif = false where obatalkes_id IN ({$obatalkes})";
        //     $update = Yii::$app->db->createCommand($sql)->execute();
        // }else{
        //     $sql = "update stokobatalkes_t set stokoa_aktif = false where obatalkes_id IN ({$listfalse})";
        //     $update = Yii::$app->db->createCommand($sql)->execute();
        //     //$update = Yii::$app->db->createCommand()->update('stokobatalkes_t',['stokoa_aktif'=>0],'obatalkes_id IN ('.$obatalkes.')')->execute();
        // }
        // return $obatalkes;
    	$fieldname = array_keys($data_obatalkes[1]);
        $fieldnames = array_keys($data_stokobatalkes[1]);
    	$save_detailobatalkes = Yii::$app->db->createCommand()->batchInsert('stokopnamedetail_t',$fieldname,$data_obatalkes)->execute();
        $save_obatalkes = Yii::$app->db->createCommand()->batchInsert('stokobatalkes_t',$fieldnames, $data_stokobatalkes)->execute();
    	return "sakses";

    }

    public function actionGetInstalasi(){
    	$request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataInstalasi();
        if(!empty($post['term'])){
        	$term = strtoupper($post['term']);
            $result->andFilterWhere(['like', 'instalasi_nama', $term]);
        }
        return $result->asArray()->all();
    }
    public function dataInstalasi(){
    	$data = Instalasi::find()->select(['instalasi_id','instalasi_nama']);
    	return $data;
    }


    public function actionDataFormulir(){
    	$result = $this->getDataFormulir();
    	$result->select(['formulirstokopname_id','noformulir']);
    	if(!empty($post['term'])){
            $term = strtoupper($post['term']);
            $result->where('noformulir = :formulir',[':formulir'=>$term]);
        }
        return $result->asArray()->all();
    }
    public function getDataFormulir(){
    	$data = InfoFormulirStokOpnameView::find();
    	return $data;
    }

     public function actionDataFormulirDetail($id){
        $result = $this->getDataDetail();
        $result->where(['formulirstokopname_id'=>$id]);
        $result_sum = $result->sum('hargajual');
        return ['data'=>$result->asArray()->all(),'data_sum'=>$result_sum,'count' => $result->count()];
    }

    public function getDataDetail(){
        $data = DetailFormulirStokOpnameView::find();
        return $data;
    }
    public function getStokOpnameNomor($lookup){
    	$sql = "select penomoran_nama,prefix,last_generate,last_number from penomoran_k where penomoran_id = '{$lookup}'";
    	$id = \Yii::$app->db->createCommand($sql)->queryOne();
    	$prefix = substr($id['last_generate'], 0,3);
		$date = substr($id['last_generate'], 3,8);
		$number = substr($id['last_generate'], -4);
		$now = date('Ymd');
		$numPrefix = "";
		if($date != $now){
			$newDate = $now;
			$newNumber = '0001';
		}else{
			$newDate = $date;
			$newNumber = $number+1;
			if(strlen($newNumber) == 1){
				$numPrefix = "000";
			}else if(strlen($newNumber) == 2){
				$numPrefix = "00";
			}else if(strlen($newNumber) == 3){
				$numPrefix = "0";
			}else{
				$numPrefix = "";
			}
		}
		$newId = $prefix.$newDate.$numPrefix.$newNumber;
		$this->updateId($lookup, $newId, $numPrefix.$newNumber);
		return $newId;
    }
    //action buat update penomoran
    public function updateId($lookup, $newId, $last_number)
    {
    	$sql = \Yii::$app->db->createCommand()->update('penomoran_k', ['last_generate'=>$newId,'last_number'=>$last_number], "penomoran_id = '{$lookup}'")->execute();
    }
    public function actionGetSum($id){
        $result = $this->getDataDetail($id);
        $result = $result->sum('hargajual');

        return $result;

    }
}
