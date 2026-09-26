<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-25 11:28:24
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-21 14:04:46
 **/

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\KelompokTindakanBpjsView;
use app\modules\v1\models\KelompokTindakanBpjs;
use app\modules\v1\models\KelompokTindakanBpjsDetail;
use app\modules\v1\models\Lookup;

class KelompokTindakanBpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelompokTindakanBpjsView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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

    public function actionIndex() {
        try{
            $request = Yii::$app->request;
            $model = new KelompokTindakanBpjsView;
            $query = $model::find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['monitorbpjsdetail'])) {
                    $detailid = $advancedFilter['monitorbpjsdetail'];
                    $query->andWhere(
                        "(
                              SELECT 1 FROM monitorbpjsdetail_m
                              WHERE monitorbpjsdetail_m.monitorbpjs_id = monitorbpjs_v.monitorbpjs_id 
                              AND monitorbpjsdetail_m.groupinacbg_id = {$detailid} AND monitorbpjsdetail_m.is_deleted = false
                              LIMIT 1
                            ) = 1"
                    );
                    unset($_GET['advanced-filter']['monitorbpjsdetail']);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDataNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select kelompoktindakan_nama from monitorbpjs_m where UPPER( kelompoktindakan_nama ) LIKE '%{$term}%' and is_deleted = false
            group by kelompoktindakan_nama
            order by kelompoktindakan_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataBPJS()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select groupinacbg_id from monitorbpjsdetail_m where UPPER( groupinacbg_id ) LIKE '%{$term}%' and is_deleted = false
            group by groupinacbg_id
            order by groupinacbg_id asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionSaveData()
    {
        $model = new KelompokTindakanBpjs;
        $request = Yii::$app->request;
        $post = $request->post();
        $groupinacbg_nama = $post['groupinacbg_id'];
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                $primaryID = $model->monitorbpjs_id;
                $detail = [];
                foreach ($groupinacbg_nama as $key => $value) {
                    $detail[] = [
                        'monitorbpjs_id' => $primaryID,
                        'groupinacbg_id' => $value,
                    ];
                }
                KelompokTindakanBpjsDetail::batchInsert($detail);
                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];
            }else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'text' => $e->getMessage(),
                'title' => 'Proses Gagal',
                'status' => 422,
            ];
        }
    }

    public function actionEditData($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = KelompokTindakanBpjs::findOne($id);
            $post = $request->post();
            $groupinacbg_nama = $post['groupinacbg_id'];
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            $test = (new KelompokTindakanBpjsDetail)->delete(['monitorbpjs_id' => $id]);
            if ($model && !empty($model)) {
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
                    $primaryID = $model->monitorbpjs_id;
                    $detail = [];
                    foreach ($groupinacbg_nama as $key => $value) {
                        $detail[] = [
                            'monitorbpjs_id' => $primaryID,
                            'groupinacbg_id' => $value,
                        ];
                    }
                    KelompokTindakanBpjsDetail::batchInsert($detail);
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'text' => $e->getMessage(),
                'title' => 'Proses Gagal',
                'status' => 422,
            ];
        }
    }

    public function actionViewData($id)
    {
        $model = new KelompokTindakanBpjsView;
        $query = $model->findOne(['monitorbpjs_id' => $id]);
        return $query;
    }

    public function actionDelete($id)
    {
        try {
            $result = (new KelompokTindakanBpjs)->delete($id);
            $test = (new KelompokTindakanBpjsDetail)->delete(['monitorbpjs_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #monitorbpjs_table# => table
    * @attribute #nama_rs# => nama rumah sakit
    * @attribute #tanggal# => Tanggal sekarang
    * @attribute #no# => No
    * @attribute #kelompoktindakan# => Kelompok Tindakan
    * @attribute #kelompokbpjs# => Kelompok BPJS
    * @attribute #status# => Status
    * @attribute #datatable# => untuk mengganti data di table
    **/

    public function actionCetakPdf()
    {
        try{
            $request = Yii::$app->request;
            $nama_rs = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
            }
            // Define model
            $model = new KelompokTindakanBpjsView;
            // Query
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->all();
            
            $print = new DocoPrint();
            $print->attributes = [
                '#monitorbpjs_table#' => $this->renderPartial('index', [
                    'detail' => $data
                ]),
                '#nama_rs#' => $nama_rs,
                '#tanggal#' => date('d-m-Y'),
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected $_title = 'KELOMPOK TINDAKAN BPJS';
    public function actionExportExcel()
    {
        try {
           // Declare emty data
            $data = [];
            $header = [];
            $footer = [];

            $nama_rs = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
            }

            if (isset($_GET['advanced-filter']['kelompoktindakan_nama'])) {
                $header[Yii::t('app', 'Kelompok Tindakan BPJS')] = $_GET['advanced-filter']['kelompoktindakan_nama'];
            }

            if (isset($_GET['advanced-filter']['monitorbpjsdetail'])) {
                $header[Yii::t('app', 'Kelompok Inacbgs')] = $_GET['advanced-filter']['monitorbpjsdetail'];
            }

            if (isset($_GET['advanced-filter']['is_active'])) {
                $header[Yii::t('app', 'Status')] = $_GET['advanced-filter']['is_active'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
            }

            $model = new KelompokTindakanBpjsView;
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            // Assign data
            if (!empty($query)) {
                // Loop
                foreach ($query as $index => $value) {
                    $newdata = [];
                    // Assign data
                    $newdata['Kelompok Tindakan'] = $value->kelompoktindakan_nama;
                    $tmp_detail = $value['monitorbpjsdetail'];
                    if (is_array($tmp_detail)) {
                        $detail = [];
                        foreach ($tmp_detail as $value1) {
                            $detail[] = $value1['groupinacbg_nama'];
                        }
                        $newdata['Kelompok BPJS'] = $value['monitorbpjsdetail']=implode(', ', $detail);
                    }
                    // $newdata['Kelompok BPJS'] = $value->monitorbpjsdetail;
                    $newdata['Status'] = ($value->is_active) ? 'Aktif' : 'Tidak Aktif' ;
                    $data[] = $newdata;
                }
            }

            $filePath = DocoHelpers::exportExcel($this->_title . " " . strtoupper($nama_rs), $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetRequest()
    {
        $lookup = KelompokTindakanBpjsView::find()
            ->where(['is_active' => 1])
            ->all();

        return ArrayHelper::map($lookup, 'groupinacbg_id', 'groupinacbg_nama');
    }

}