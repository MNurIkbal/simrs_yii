<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-25 11:28:24
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-02-21 14:24:44
 **/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\KondisiKeluarView;
use app\modules\v1\models\KondisiKeluar;
use app\modules\v1\models\CaraKeluar;


class KondisiKeluarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KondisiKeluarView';

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
        $model = new KondisiKeluarView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataKode()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select kondisikeluar_kode from kondisikeluar_m where UPPER( kondisikeluar_kode ) LIKE '%{$term}%' and is_deleted = false
            group by kondisikeluar_kode
            order by kondisikeluar_kode asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select kondisikeluar_nama from kondisikeluar_m where UPPER( kondisikeluar_nama ) LIKE '%{$term}%' and is_deleted = false
            group by kondisikeluar_nama
            order by kondisikeluar_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataCara()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select carakeluar_nama from carakeluar_m where UPPER( carakeluar_nama ) LIKE '%{$term}%' and is_deleted = false
            group by carakeluar_nama
            order by carakeluar_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamalain()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select kondisikeluar_namalain from kondisikeluar_m where UPPER( kondisikeluar_namalain ) LIKE '%{$term}%' and is_deleted = false
            group by kondisikeluar_namalain
            order by kondisikeluar_namalain asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionSaveData()
    {
        $model = new KondisiKeluar;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                return ['message' => 'Data Berhasil di simpan'];
            }else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
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

    public function actionDelete($id)
    {
        try {
            $result = (new KondisiKeluar)->delete($id);
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
    * @attribute #kondisikeluar_table# => table
    * @attribute #nama_rs# => nama rumah sakit
    * @attribute #tanggal# => Tanggal sekarang
    **/
    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $nama_rs = '';
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['nama_rs'])) {
                $nama_rs = $_GET['advanced-filter']['nama_rs'];
            }
        }
        // Define model
        $model = new KondisiKeluarView;
        // Query
        $query = $model::find();
        // Doco active filter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#kondisikeluar_table#' => $this->renderPartial('index', [
                'detail' => $data
            ]),
            '#nama_rs#' => $nama_rs,
            '#tanggal#' => date('d-m-Y'),
        ];
        $print->Output();
    }

    protected $_title = 'KONDISI KELUAR';
    public function actionExportExcel()
    {
        try {
           // Declare emty data
            $data = [];
            $header = [];

            $nama_rs = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
            }
            // Define model
            $model = new KondisiKeluarView;
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            // Assign data
            if (!empty($query)) {
                // Loop
                foreach ($query as $index => $value) {
                    $newdata = [];
                    // Assign data
                    $newdata['Kode'] = $value->kondisikeluar_kode;
                    $newdata['Cara Keluar'] = $value->carakeluar_nama;
                    $newdata['Kondisi Keluar'] = $value->kondisikeluar_nama;
                    $newdata['Nama Lainnya'] = $value->kondisikeluar_namalain;
                    $newdata['Status'] = ($value->is_active) ? 'Aktif' : 'Tidak Aktif' ;
                    $newdata['Catatan'] = $value->catatan;
                    $data[] = $newdata;
                }
            }

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Kode' => 'Tanggal Unduh : ' . date('d M Y'),
                ]
            ];

            $filePath = DocoHelpers::exportExcel($this->_title . " " . strtoupper($nama_rs), $data, [], [],$footer,[],true);
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

    public function actionViewData($id)
    {
        $model = new KondisiKeluar;
        $query = $model->findOne(['kondisikeluar_id' => $id]);
        $data['data_update'] = $query;
        $data['data_nya'] = $this->actionCaraKeluarData();
        return $data;
    }

    public function actionCaraKeluarData()
    {
        $data = ArrayHelper::map(CaraKeluar::find()->all(),
            'carakeluar_id','carakeluar_nama');
        return $data;
    }

    public function actionEditData($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = KondisiKeluar::findOne($id);
            $post = $request->post();
            if ($model && !empty($model)) {
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
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

}