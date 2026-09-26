<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-11 11:14:49
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-24 13:46:41
 * desc: transaksi penyimpanan dokumen rekam medis backend controller
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\KembaliRm;
use app\modules\v1\models\PasienView;
use app\modules\v1\models\KirimDokumenRmView;
use app\modules\v1\models\DokRekamMedis;
use app\modules\v1\models\InformasiPengirimanRmView;
use app\modules\v1\models\LokasiRak;


class TransaksiPenyimpananDokumenController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\KembaliRm';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["create"] = ["POST"];
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
            //define data dan model
            $model = new KembaliRm;            
            $data_pengiriman = $this->getDataPengiriman($post['no_pengiriman']);
            $data_pengiriman = $data_pengiriman->asArray()->one();
                        
            //define data yg di simpan
            $model->pengirimanrm_id = $post['no_pengiriman'];            
            $model->pasien_id = $post['no_rekam_medik'];
            $model->dokrekammedis_id = $post['dokrm_id'];                        
            $model->petugaspenerima_id = "";
            $model->ruanganasal_id = $data_pengiriman['ruanganpengirim_id'];
            $model->tglkembali = $post['tgl_akhir_masuk']; 
            $model->status_indexing = $post['status_indexing'];                       
            $model->status_assembling = $post['status_assembling'];            

            if(!empty($post['dokrm_id'])){
                $model_dokumen = DokRekamMedis::find()->where(['dokrekammedis_id'=>$post['dokrm_id']])->one();
                if($model_dokumen){                    
                    $model_dokumen->subrak_id = $post['no_sub_rak'];
                    $model_dokumen->lokasirak_id = $post['no_rak'];             

                    if($model->save() && $model_dokumen->save()){
                        return [
                                    'message' => 'Data Berhasil di simpan',
                                ];
                    }else{
                        $err = $model_dokumen->getErrors();
                        return $err;   
                    }         
                }else{                    
                    if($model->save()){
                        return [
                                    'message' => 'Data Berhasil di simpan',
                                ];
                    }else{
                        $errors = DocoHelpers::parseError($model->errors,'TransaksiPenyimpananDokumenForm');

                            return [
                                'data' => $errors,
                                'status' => 422
                            ];
                    }    
                }
                
            }else{                

                $model_dokumen = new DokRekamMedis;
                $model_dokumen->warnadokrm_id = $post['warnadokrm_id'];
                $model_dokumen->subrak_id = $post['no_sub_rak'];
                $model_dokumen->lokasirak_id = $post['no_rak'];
                $model_dokumen->pasien_id = $post['no_rekam_medik'];                
                $model_dokumen->tglrekammedis = $post['tgl_rekam_medik'];
                $model_dokumen->tglmasukrak = date('Y-m-d');
                $model_dokumen->statusrekammedis = "1";                

                if($model_dokumen->save()){
                    return [
                                'message' => 'Data Berhasil di simpan',
                            ];
                }else{
                    $err = $model_dokumen->getErrors();
                    return $err;   
                }
            }
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    public function actionGetListData(){
    	try{
            $request = Yii::$app->request;            
            $result_pasien = PasienView::find();
            $result_pengiriman = KirimDokumenRmView::find();
            $result_rak = $this->getRak();
            $data_warna = $this->getWarna();
            $data_rak = [];                        
            $data_subrak = [];        
            foreach ($result_rak as $key => $value) {
                if(!isset($data_rak[$value['lokasirak_id']])){
                    $data_rak[$value['lokasirak_id']] = $value;
                }
                if(!empty($value['rak_id'])){
                    $subrak = [
                        'subrak_id'=>$value['subrak_id'],
                        'subrak_nama'=>$value['subrak_nama'],
                    ];
                    $data_subrak[$value['lokasirak_id']][] = $subrak;
                }

            }
            $return = [
                    'data_rm'=>$result_pasien->asArray()->all(),
                    'data_pengiriman'=>$result_pengiriman->asArray()->all(),
                    'data_rak'=>$data_rak,
                    'data_subrak'=>$data_subrak,
                    'data_warna'=>$data_warna,

                ];  
            return $return;
        }catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    public function actionDataRm(){
        try {

            $request = Yii::$app->request;            
            $result = $this->getDataRm()
                           ->limit($request->post('length',10))
                           ->offset($request->post('start',0));
            if($no_rekam_medik = $request->post('no_rekam_medik')){                
                $result->andFilterWhere(['ILIKE','pasien_v.no_rekam_medik',$no_rekam_medik]);
            }
            if($nama_pasien = $request->post('nama_pasien')){                
                $result->andFilterWhere(['ILIKE','pasien_v.nama_pasien',$nama_pasien]);
            }
            if($tanggal_lahir = $request->post('tanggal_lahir')){                
                $result->andFilterWhere(['ILIKE','pasien_v.tanggal_lahir',$tanggal_lahir]);
            }

            return [
                'data'=>$result->asArray()->all(),
                'count'=>$result->count(),
            ];    

        }catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    public function actionDataPengiriman(){
        try {

            $request = Yii::$app->request;            
            $result = $this->getDataPengiriman()
                           ->limit($request->post('length',10))
                           ->offset($request->post('start',0));
            if($nomor_pengiriman = $request->post('nomor_pengiriman')){                
                $result->andFilterWhere(['ILIKE','kirimdokumenrm_v.nomor_pengiriman',$nomor_pengiriman]);
            }
            if($tgl_pengirimanrm = $request->post('tgl_pengirimanrm')){                
                $result->andFilterWhere(['ILIKE','kirimdokumenrm_v.tgl_pengirimanrm',$tgl_pengirimanrm]);
            }
            if($ruangan_asal = $request->post('ruangan_asal')){                
                $result->andFilterWhere(['ILIKE','kirimdokumenrm_v.ruangan_asal',$ruangan_asal]);
            }
            if($instalasi_asal = $request->post('instalasi_asal')){                
                $result->andFilterWhere(['ILIKE','kirimdokumenrm_v.instalasi_asal',$instalasi_asal]);
            }
            if($no_rekam_medik = $request->post('no_rekam_medik')){                
                $result->andFilterWhere(['ILIKE','kirimdokumenrm_v.no_rekam_medik',$no_rekam_medik]);
            }

            return [
                'data'=>$result->asArray()->all(),
                'count'=>$result->count(),
            ];    

        }catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    public function actionGetDokumenData($id){
        try {
            $data = $this->getDataRak($id);  
            $return = [
                    'data_rak'=>$data->asArray()->all(),                    
                ];  
             return $return;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];   
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
        

    }    

    public function getDataRm(){
        $data = PasienView::find()->select([
                                            'pasien_v.pasien_id',
                                            'pasien_v.no_rekam_medik',
                                            'pasien_v.nama_pasien',
                                            'pasien_v.tanggal_lahir',
                                            'pasien_v.tgl_rekam_medik',
                                          ]);        
        return $data;

    }
    public function getDataPengiriman($id = null){
        $data = KirimDokumenRmView::find()->select([
                                            'kirimdokumenrm_v.nomor_pengiriman',
                                            'kirimdokumenrm_v.tgl_pengirimanrm',
                                            'kirimdokumenrm_v.ruangan_asal',
                                            'kirimdokumenrm_v.ruanganpengirim_id',
                                            'kirimdokumenrm_v.instalasi_asal',
                                            'kirimdokumenrm_v.instalasi_id',
                                            'kirimdokumenrm_v.no_rekam_medik',
                                            'kirimdokumenrm_v.pengirimanrm_id'
                                          ]);
        if($id){
            $data->where(['kirimdokumenrm_v.pengirimanrm_id'=>$id]);
        }
        return $data;

    }
    public function getDataRak($id = null){        
        $data = DokRekamMedis::find()->select([
                                        'dokrekammedis_m.dokrekammedis_id',
                                        'dokrekammedis_m.subrak_id',
                                        'dokrekammedis_m.lokasirak_id',
                                    ]);
        $data->where(['dokrekammedis_m.pasien_id'=>$id]);        
        return $data;

    }
    public function getRak(){
        // $data = LokasiRak::find()->select([
        //                                     'lokasirak_m.lokasirak_id',
        //                                     'lokasirak_m.lokasirak_nama',
        //                                  ]);
        $data = \Yii::$app->db->createCommand("SELECT lokasirak_m.lokasirak_id, lokasirak_m.lokasirak_nama, subrak_m.subrak_id,subrak_m.lokasirak_id as rak_id, subrak_m.subrak_nama
            FROM lokasirak_m
            LEFT JOIN subrak_m ON subrak_m.lokasirak_id = lokasirak_m.lokasirak_id
            ORDER BY lokasirak_m.lokasirak_id ASC
            ")->queryAll();
        return $data;
    }
    public function getWarna()
    {
        $data = \Yii::$app->db->createCommand("SELECT warnadokrm_id,warnadokrm_kodewarna FROM warnadokrekammedik_m where is_deleted = 'f'")->queryAll();
        return $data;
    }

}
