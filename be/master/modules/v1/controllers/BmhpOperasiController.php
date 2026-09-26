<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\PaketBmhp;
use app\modules\v1\models\PaketBmhpView;

class BmhpOperasiController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\PaketBmhp';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new PaketBmhpView;
            $query = $model::find();
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['daftartindakan_nama'])) {
                    $query->andWhere(['ILIKE', 'daftartindakan_nama', $advancedFilter['daftartindakan_nama']]);
                }
            }

            // return $query->all();exit();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSave($id = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            if($id) {
                $data = $post['data']['PaketBmhpForm'];
                $model = PaketBmhp::findOne($id);
                $model->attributes = $data;
                if($model->validate()) {
                    $cekExist = $this->cekExist($data['daftartindakan_id'], $data['obatalkes_id']);
                    if($cekExist) {
                        $model = $cekExist;
                        $model->qty_pemakaian = $data['qty_pemakaian'];
                    }
                    $model->save();
                    $transaction->commit();
                }
                else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            else {
                $dataJson = $request->post('data',"{}");
                $data = json_decode($dataJson,true);
                if(!empty($data)) {
                    $dataInsert = [];
                    $tmp = [];
                    $obatAlkesTmp = [];
                    foreach ($data as $key => $value) {
                        if(!empty($value)) {
                            foreach ($value as $k => $v) {
                                $obatAlkesTmp[] =  $v['obatalkes_id'];      
                                $tmp[] = $v['daftartindakan_id'];
                                $dataInsert[] = [
                                    'obatalkes_id' => $v['obatalkes_id'],
                                    'satuankecil_id' => $v['satuankecil_id'],
                                    'daftartindakan_id' => $v['daftartindakan_id'],
                                    'qty_pemakaian' => $v['qty_pemakaian'],
                                ];
                            }
                        }
                    }

                    $delete = (new PaketBmhp)->delete([
                        'daftartindakan_id' => $tmp, 
                        'obatalkes_id' => $obatAlkesTmp]);

                    PaketBmhp::batchInsert($dataInsert);
                    $transaction->commit();
                }
                else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        return $this->getData(['paketbmhp_id'=>$id])->asArray()->one();
    }

    private function getData($filter = null)
    {
        $returnData = PaketBmhp::find()->joinWith(['daftartindakan', 'obatalkes', 'satuankecil']);

        if($filter) {
            $returnData->where($filter);
        }

        return $returnData;
    }

    private function cekExist($daftartindakan_id, $obatalkes_id)
    {
        $query = PaketBmhp::find()
            ->where([
                'daftartindakan_id' => $daftartindakan_id,
                'obatalkes_id' => $obatalkes_id
            ])
            ->one();

        return $query;
    }
}