<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\RuangPemakai;
use app\modules\v1\models\Ruangan;

class ProfilUserController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LoginPemakai';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-nama"] = ["POST", "GET"];        
        $verbs["update"] = ["POST", "PUT"];
        $verbs["rubah-password"] = ["POST","PUT"];
        $verbs["get-pengguna"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        // unset($actions['delete']);        
        // unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {        
        try {
            $request = Yii::$app->request;            
            $result = $this->getData()
                           ->limit($request->post('length',10))
                           ->offset($request->post('start',0));
            if($name = $request->post('nama')){
                $result->andWhere(['loginpemakai_k.loginpemakai_id'=>$name]);
                // $result->andFilterWhere(['ILIKE','loginpemakai_k.loginpemakai_id',$name]);
            }
            $status = $request->post('is_active');

            $status = true;
            $result->andWhere(['loginpemakai_k.is_active'=>$status]);
            $result->joinWith([
                        'ruangan' => function($query){
                            $query->from('ruangan_m');
                        },
                        'jabatan' => function($query){
                            $query->from('jabatan_m');
                        }

                    ]);

            return [
                'data'=>$result->asArray()->all(),
                'count'=>$result->count(),
            ];    
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
        

    }
    public function actionUpdate($id){
        try{
            $request = Yii::$app->request;            
            $model_loginpemakai = LoginPemakai::findOne($id);
            //$model_pegawai = Pegawai::findOne($model_loginpemakai->pegawai_id);            

            if($request->post() && !empty($model_loginpemakai)){
                $post = $request->post();                                
                $model_loginpemakai->photouser = $post['photouser'];
                $model_loginpemakai->nama_pemakai = $post['nama_pemakai'];

                                            
                if($model_loginpemakai->save()){
                    try{
                        $save_pegawai = \Yii::$app->db->createCommand()->update('pegawai_m',['jabatan_id'=>$post['jabatan_id']], 'pegawai_id = :pegawai_id',[':pegawai_id'=>$model_loginpemakai->pegawai_id])->execute();
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    }catch (Exception $ex){
                        return $ex->getMessage();
                    }
                    
                }else{
                    $errors = DocoHelpers::parseError($model_loginpemakai->errors,'LoginPemakaiForm');

                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        }catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }

    }
    public function actionView($id){
        $result = $this->getData($id);
        $result->joinWith([
                    'jabatan' => function($query){
                            $query->from('jabatan_m');
                        }
                ]);            
       return $result->asArray()->one();
    }
    public function actionGetPengguna(){
        try{
            $request = Yii::$app->request;
            $result = $this->getNama();
            $status = true;
            $result->andWhere(['loginpemakai_k.is_active'=>$status]);
            return ['data'=>$result->asArray()->all()];
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

    public function actionGetJabatan(){
        try{
            $request = Yii::$app->request;
            $status = true;
            $result = Jabatan::find()->select(['jabatan_id','jabatan_nama']);
            $result->andWhere(['is_active'=>$status]);

            return ['data'=>$result->asArray()->all()];
        }catch (\Exception $e){
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
    public function actionRubahPassword(){
        try{
            $request = Yii::$app->request; 
            if($request->post()){
                $post = $request->post();      
                $result = LoginPemakai::find()->where(['loginpemakai_k.nama_pemakai'=>$post['nama_pemakai']]);
                $data = $result->asArray()->one();

                if(!empty($data)){                    
                        $save_pemakai = \Yii::$app->db->createCommand()
                                                        ->update('loginpemakai_k',
                                                            ['katakunci_pemakai'=>$pass], 
                                                            'loginpemakai_id = :loginpemakai_id',
                                                            [':loginpemakai_id'=>$data['loginpemakai_id']])
                                                        ->execute();                        
                        if($save_pemakai){
                            return[
                                'message'=>"Sukses!",
                            ];
                        }else{
                            \Yii::$app->response->statusCode = 500;
                            return[
                                'message'=>"Terjadi Kesalahan!",
                            ];      
                        } 
                    
                }else{
                    \Yii::$app->response->statusCode = 500;
                    return[
                        'message'=>'Nama Pengguna Tidak Ditemukan',
                    ];
                }
                
                
            }            
            

        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    private function getData($id = null){
        $data = LoginPemakai::find()
                    ->select([
                        'loginpemakai_k.loginpemakai_id',
                        'loginpemakai_k.nama_pemakai',
                        'loginpemakai_k.ruangan_aktifitas',
                        'loginpemakai_k.pegawai_id',   
                        'loginpemakai_k.photouser',                                         
                    ]);

        if($id){
            $data->where(['loginpemakai_k.loginpemakai_id'=>$id]);
        }
        return $data;
    }
    private function getNama(){
        $data = LoginPemakai::find()->select(['loginpemakai_id','nama_pemakai']);
        return $data;
    }



    

}