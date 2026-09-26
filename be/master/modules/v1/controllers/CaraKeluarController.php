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
use app\modules\v1\models\CaraKeluarView;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\Lookup;

class CaraKeluarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\CaraKeluarView';

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
        $request = Yii::$app->request;
        $model = new CaraKeluarView;
        $query = $model::find();

        if($request->get('advanced-filter')) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['carakeluarinacbg_nama'])) {
                $carakeluarinacbg_id = $advancedFilter['carakeluarinacbg_nama'];
                $query->andWhere(['carakeluarinacbg_id' => $carakeluarinacbg_id]);
                unset($_GET['advanced-filter']['carakeluarinacbg_nama']);
            }
        }

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
        
        $sql = "select carakeluar_kode from carakeluar_m where UPPER( carakeluar_kode ) LIKE '%{$term}%' and is_deleted = false
            group by carakeluar_kode
            order by carakeluar_kode asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNama()
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
        
        $sql = "select carakeluar_namalain from carakeluar_m where UPPER( carakeluar_namalain ) LIKE '%{$term}%' and is_deleted = false
            group by carakeluar_namalain
            order by carakeluar_namalain asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionSaveData()
    {
        $model = new CaraKeluar;
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
            $result = (new CaraKeluar)->delete($id);
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
    * @attribute #carakeluar_table# => table
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
        $model = new CaraKeluarView;
        // Query
        $query = $model::find();
        // Doco active filter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#carakeluar_table#' => $this->renderPartial('index', [
                'detail' => $data
            ]),
            '#nama_rs#' => $nama_rs,
            '#tanggal#' => date('d-m-Y'),
        ];
        $print->Output();
    }

    protected $_title = 'CARA KELUAR';
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

            if (isset($_GET['advanced-filter']['carakeluar_kode'])) {
                $header[Yii::t('app', 'Kode')] = $_GET['advanced-filter']['carakeluar_kode'];
            }

            if (isset($_GET['advanced-filter']['carakeluar_nama'])) {
                $header[Yii::t('app', 'Cara Keluar')] = $_GET['advanced-filter']['carakeluar_nama'];
            }

            if (isset($_GET['advanced-filter']['carakeluar_namalain'])) {
                $header[Yii::t('app', 'Nama Lainnya')] = $_GET['advanced-filter']['carakeluar_namalain'];
            }

            if (isset($_GET['advanced-filter']['carakeluarinacbg_nama'])) {
                $header[Yii::t('app', 'Cara Keluar INACBG')] = $_GET['advanced-filter']['carakeluarinacbg_nama'];
            }

            if (isset($_GET['advanced-filter']['is_active'])) {
                $header[Yii::t('app', 'Status')] = $_GET['advanced-filter']['is_active'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
            }

            if (isset($_GET['advanced-filter']['catatan'])) {
                $header[Yii::t('app', 'Catatan')] = $_GET['advanced-filter']['catatan'];
            }

            $model = new CaraKeluarView;
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            // Assign data
            if (!empty($query)) {
                // Loop
                foreach ($query as $index => $value) {
                    $newdata = [];
                    // Assign data
                    $newdata['Kode'] = $value->carakeluar_kode;
                    $newdata['Cara Keluar'] = $value->carakeluar_nama;
                    $newdata['Nama Lainnya'] = $value->carakeluar_namalain;
                    $newdata['Status'] = ($value->is_active) ? 'Aktif' : 'Tidak Aktif' ;
                    $newdata['Catatan'] = $value->catatan;
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

    public function actionViewData($id)
    {
        $model = new CaraKeluar;
        $query = $model->findOne(['carakeluar_id' => $id]);
        return $query;
    }

    public function actionEditData($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = CaraKeluar::findOne($id);
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

    public function actionGetRequest()
    {
        $lookup = Lookup::find()
            ->where(['lookup_type' => 'carapulang_inacbg', 'is_active' => 1])
            ->all();

        return ArrayHelper::map($lookup, 'lookup_id', 'lookup_name');
    }

}