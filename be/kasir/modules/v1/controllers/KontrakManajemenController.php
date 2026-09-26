<?php

namespace app\modules\v1\controllers;
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\KontrakPenjaminView;
use app\modules\v1\models\KontrakPenjamin;
use app\modules\v1\models\PenjaminGrade;
use app\modules\v1\payload\PenjaminGradePayload;
use app\modules\v1\models\KontrakPenjaminDetail;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\TipeDiskon;
use app\modules\v1\cache\Cache;
use Doco\components\DocoConstansId;

class KontrakManajemenController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KontrakPenjaminView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        // unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new KontrakPenjaminView;
        $query = $model::find();
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected $_title = "Manajemen Kontrak";

    public function actionGetKonfigSystem()
    {
        $konfig = [];
        try {
            $konfig = Cache::getKonfigSistem();
        } catch (Exception $e) {
            return $konfig;
        }
        return $konfig;
    }

    public function actionGetListLob(){

        $request = Yii::$app->request;
        $model = new Lookup;
        $query = $model::find()->where(['lookup_type'=>'LOB']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetTipeDiskon(){
        $request = Yii::$app->request;
        $model = new TipeDiskon;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    public function actionGenerateApi()
    {
        $request = Yii::$app->request;
        $model = new KontrakPenjaminView;
        $result = $model::find()->all();
        $result['carabayar'] = [];
        $result['penjamin'] = [];

        try{
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = $result['carabayar']['response'];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = $result['penjamin']['response'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }

    public function actionViewUpdate($id){
        $header = KontrakPenjamin::find()
            ->select('kontrakpenjamin_m.*, penjamin_m.penjamin_nama')
            ->leftJoin('penjamin_m',"kontrakpenjamin_m.penjamin_id = penjamin_m.penjamin_id")
            ->where(['kontrakpenjamin_id' => $id])->asArray()->one();     

        return [
            'header' => $header,
        ];
    }

    public function actionViewGrade($id){
        $grade = KontrakPenjaminDetail::find()
            ->select('kontrakpenjamindetail_m.*, lookup_m.lookup_name, lookup_m.lookup_id, tipediskon_m.tipediskon_nama')
            ->leftJoin('lookup_m',"lookup_m.lookup_id = kontrakpenjamindetail_m.lob_id")
            ->leftJoin('tipediskon_m',"tipediskon_m.tipediskon_id = kontrakpenjamindetail_m.tipediskon_id")
            ->where(['kontrakpenjamindetail_m.kontrakpenjamin_id' => $id])->asArray()
            ->all();

        return $grade;
    }
    
    public function actionMappingGrades($penjamin_id){
        $request = Yii::$app->request;
        $payload = new PenjaminGradePayload;
        $model = new PenjaminGrade;
        $payload->attributes = $request->get();

        if($payload->validate()){
            try {          
                $mapping_grade = $model::find()
                ->select(['penjamingrade_id','penjamin_id','grade'])
                ->where(['penjamin_id' => $penjamin_id])->asArray()
                ->all();
                // return $mapping_grade;
                if(empty($mapping_grade)){
                    return [
                        'message' => 'Data tidak ditemukan',
                        'status' => 422,
                    ];
                }                
                return $mapping_grade;
            }
            catch (\yii\db\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        
        } else {
            return [
                'message' => $payload->errors,
                'status' => 422,
            ];
        }
        
        
        
    }

    public function actionCheckKontrak()
    {   
        $request = Yii::$app->request;
        $post = $request->post();
        $postData = $post['no_kontrak'];
        $model = new KontrakPenjamin;
        $result = $model::find()
        ->select(['no_kontrak'])->where(['no_kontrak'=> $postData])->asArray()->all();
        if(!empty($result)){
            return true;
        } else return false;
        //return $result;
    }

    public function actionSaveMapGrade()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $model = new PenjaminGrade;

        $model->grade =$post['grade'];
        $model->penjamin_id =$post['penjamin'];
        
        
        try {          
             if($model->validate()&& $model->save()) {
                $idParent = $model->getPrimaryKey() ;                  

                $transaction->commit();
                $response = [
                    'text' => 'Mapping Grade berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'penjamingrade_id' => $idParent,
                ];                    
                return $response;
             } 
            else {
                return [
                    'status' => 422,
                    'data' => DocoHelpers::parseError($model->errors,'PenjaminGradeForm'),
                ];
            }    
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSave()
    {   
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $postData = $post['data'];
        $dataJsonGrade = $request->post('cacheGrade',"{}");
        $dataGrade = json_decode($dataJsonGrade,true);      
        $model = new KontrakPenjamin;
        $model_detail = new KontrakPenjaminDetail;
        try {            
            $model->attributes = $postData;
            
            if((strtotime(date('d-M-Y')) - strtotime($model->tgl_selesai)) > 0){
                $model->is_active = FALSE;
            } 
             if($model->validate()&& $model->save()) {
                $insert = [];
                $idParent = $model->getPrimaryKey() ;

                if(!empty($dataGrade)) {
                    foreach ($dataGrade as $key => $value) {
                        $insert[] = [
                            'kontrakpenjamin_id' => (int)$idParent,
                            'lob_id' => (int)$value['lookup_id'],
                            'lookup_value' => (int)$value['lookup_value'],
                            'tipediskon_id' => (int)$value['tipediskon_id'],
                            'penjamingrade_id' => (int)$value['penjamingrade_id'],
                            'grade' => $value['grade'],
                            'is_active' => TRUE,
                        ];
                    };
                     KontrakPenjaminDetail::batchInsert($insert);
                };     

                $transaction->commit();
                $response = [
                    'text' => 'Grade berhasil disimpan',
                    'title' => 'Proses berhasil !',
                ];
                
                return $response;
             } 
            else {
                return [
                    'status' => 422,
                    'data' => DocoHelpers::parseError($model->errors,'KontrakPenjaminForm'),
                ];
            }
            
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    
}
