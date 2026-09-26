<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-10 15:00
 * @Last Modified by:   Doconb-Bandung
 * @Description: controller untuk master Perujuk Pasien (Pendaftaran) 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\Perujuk;
use app\modules\v1\models\PerujukView;
use Doco\components\DocoConstants;

class PerujukController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Perujuk';

    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'Partner' => [
                        'result' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'Partner' => [
                        'result' => true
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Partner' => [
                        'result' => true
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST"];
        $verbs["delete"] = ['DELETE', 'POST'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        Yii::error('a');
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        $model = new Perujuk;
        $query = $model::find()
            ->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama','asalrujukan_m.asalrujukan_id']);
            }])
             ->where(['asalrujukan_m.is_deleted' => 'f']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
}

    public function actionCreate(){
        $request = Yii::$app->request;
        $model = new Perujuk();
        try {            
            $post = $request->post();
            $model->attributes = $post;
            if (isset($post['asalrujukan_kode'])) {
                if ($asalrujukan = AsalRujukan::find()->where(['asalrujukan_kode' => $post['asalrujukan_kode']])->one()) {
                    $model->asalrujukan_id = $asalrujukan->getPrimaryKey();
                }
            }
            if($model->validate()){
                if($model->save()){
                    Yii::$app->cache->delete(DocoConstants::CACHE_PERUJUK);
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Tersimpan',
                        'id' => $model->getPrimaryKey(),
                        'model' => 'PERUJUK',
                    ];
                }else{
                    return ['status'=> 422, 'data'=>$model->errors];
                }
            }else{
                return ['status'=> 422, 'data'=>$model->errors];
            }
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionUpdate(){
        $request = Yii::$app->request;
        try {
            $clause = [];
            $post = $request->post();
            if ($id = $request->get('id', null)) {
                $clause['perujuk_id'] = $id;
            }
            foreach (['perujuk_id', 'sync_id'] as $key) {
                if (array_key_exists($key, $post)) {
                    $clause[$key] = $post[$key];
                }
            }
            if (empty($clause)) {
                throw new \Exception('perujuk_id or sync_id is required.');
            }
            $model = Perujuk::find(true)->where($clause)->one();
            if (!$model) {
                throw new \Exception('data is not exists.');
            }
            $model->attributes = $post;
            if (isset($post['asalrujukan_kode'])) {
                if ($asalrujukan = AsalRujukan::find()->where(['asalrujukan_kode' => $post['asalrujukan_kode']])->one()) {
                    $model->asalrujukan_id = $asalrujukan->getPrimaryKey();
                }
            }
            // return $model->attributes;
            if($model->validate() && $model->save()){
                Yii::$app->cache->delete(DocoConstants::CACHE_PERUJUK);
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil Diubah',
                        'id' => $model->getPrimaryKey(),
                        'model' => 'PERUJUK',
                    ];
            }else{
                return $model->errors;
            }
            
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelete(){
        $request = Yii::$app->request;
        try {
            $clause = [];
            $post = $request->post();
            if ($id = $request->get('id', null)) {
                $clause['perujuk_id'] = $id;
            }
            foreach (['perujuk_id', 'sync_id'] as $key) {
                if (array_key_exists($key, $post)) {
                    $clause[$key] = $post[$key];
                }
            }
            if (empty($clause)) {
                throw new \Exception('perujuk_id or sync_id is required.');
            }
            $model = Perujuk::find(true)->where($clause)->one();
            if (!$model) {
                throw new \Exception('data is not exists.');
            }
            if ($model->delete()){
                Yii::$app->cache->delete(DocoConstants::CACHE_PERUJUK);
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil',
                        'text' => 'Data Berhasil dihapus',
                        'id' => $model->getPrimaryKey(),
                        'model' => 'PERUJUK',
                    ];
            }else{
                return $model->errors;
            }
            
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table 
    **/
    public function actionExportPdf()
    {
        // Get perujuk
        $mPerujuk = new PerujukView;
        $query = $mPerujuk->find();

        $model = DocoRestActiveFilter::advancedFilter($mPerujuk, $query);
        $model = $model->all();

        // Check model
        if (!empty($model)) {
            // Header
            $header = array(
                Yii::t('app', "Perujuk") => 'Perujuk',
            );

            // Print
            $print = new DocoPrint();

            // Assign attributes
            $print->attributes = [
                '#table_exportpdf#' => $this->renderPartial('pdf', [
                    'header' => $header,
                    'model' => $model,
                ]),
            ];

            // Print output
            $print->Output();
        }
    }

    // Export excel
    public function actionExportExcel()
    {
        // Try catch
        try {
            // Model
            $mPerujuk = new PerujukView;
            $query = $mPerujuk->find();

            $asal_rujukan = '-';
            $kode_perujuk = '-';
            $nama_perujuk = '-';

            if(isset($_GET['advanced-filter']['asalrujukan_id'])) {
                $data_asal_rujukan = AsalRujukan::find()->where(['asalrujukan_id' => $_GET['advanced-filter']['asalrujukan_id']])->one();
                $asal_rujukan = ($data_asal_rujukan) ? $data_asal_rujukan['asalrujukan_nama'] : '-';
            }
    
            if(isset($_GET['advanced-filter']['namaperujuk'])){
                $nama_perujuk = $_GET['advanced-filter']['namaperujuk'];
            }
    
            if(isset($_GET['advanced-filter']['perujuk_kode'])) {
                $kode_perujuk = $_GET['advanced-filter']['perujuk_kode'];
            }

            $model = DocoRestActiveFilter::advancedFilter($mPerujuk, $query);
            $model = $model->asArray()->all();

            $result = [];

            foreach($model as $key => $value) {
                $status = $value['is_active'] == "true" ? "Aktif" : "Tidak Aktif";
                $newValue = [];

                $newValue['Asal Rujukan']   = $value['asalrujukan_nama'];
                $newValue['Nama Perujuk']   = $value['namaperujuk'];
                $newValue['Kode Perujuk']   = $value['perujuk_kode'];
                $newValue['Spesialis']      = $value['spesialis'];
                $newValue['Alamat Lengkap'] = $value['alamatlengkap'];
                $newValue['Nomor Telepon']  = $value['notelp'];
                $newValue['Status']         = $status;
                
                $result[$key] = $newValue;
            }

            // Header
            $header = array(
                Yii::t('app', "Asal Rujukan") => $asal_rujukan,
                Yii::t('app', "Kode Perujuk") => $kode_perujuk,
                Yii::t('app', "Nama Perujuk") => $nama_perujuk,
            );

            $filePath = DocoHelpers::exportExcel("Master Perujuk", $result, $header, [], [], [], true);
            
            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message 
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
