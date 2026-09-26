<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 11:31:06
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-27 17:23:11
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\PemeriksaanLab;

class JenisPemeriksaanLabController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisPemeriksaanLab';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $_GET['expand'] = $request->get('expand', 'kelompokpemeriksaanlab_m');

            $model = new JenisPemeriksaanLab;

            $query = $model::find()->joinWith(['kelompokPemeriksaanLab' => function($query) {
                $query->select(['kelompokpemeriksaanlab_m.nama_kelompok', 'kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id']);
            }])->where([JenisPemeriksaanLab::tableName().'.is_deleted' => false]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                            'kelompokpemeriksaanlab_m.nama_kelompok' => SORT_ASC
                            ]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        try {
            $model = new JenisPemeriksaanLab;
            $model->attributes = $request->post();
            if($model->validate()){
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'JenisPemeriksaanLabForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'JenisPemeriksaanLabForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = JenisPemeriksaanLab::findOne($id);
            if($model->validate()){
                if ($request->post() && !empty($model)) {
                    $model->attributes = $request->post();
                    if ($model->update()) {
                        return [
                            'message' => 'Data Berhasil di ubah',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'JenisPemeriksaanLabForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'JenisPemeriksaanLabForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    // Action get kelompok by jenis id
    public function actionGetKelompokByJenisId($id)
    {
        // Try catch
        try {
            // Define model
            $model = JenisPemeriksaanLab::findOne($id);

            // Check kelompok
            if ($model->kelompokPemeriksaanLab) {
                // Return
                return $model->kelompokPemeriksaanLab;
            }
            else {
                // Return
                return null;
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
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
        // Try catch
        try {
            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new JenisPemeriksaanLab;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Check model
            if (!empty($model)) {
                // Header
                $header = array();
                
                // Print
                $print = new DocoPrint();

                // Assign attributes
                $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $query,
                    ]),
                ];

                // Print output
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Export excel
    /*public function actionExportExcel()
    {
        // Try catch
        try {
            // Declare emty data
            $data = array();
            $header = array();

            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new JenisPemeriksaanLab;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Assign data
            if (!empty($query)) {
                // Declare counter
                $counter = 0;

                // Loop
                foreach ($query as $index => $value) {
                    // Assign data
                    $data[$counter]['Kode'] = !empty($value->jenispemeriksaanlab_kode) ? $value->jenispemeriksaanlab_kode : '';
                    $data[$counter]['Kelompok Pemeriksaan'] = !empty($value->kelompokPemeriksaanLab->nama_kelompok) ? $value->kelompokPemeriksaanLab->nama_kelompok : '';
                    $data[$counter]['Jenis Pemeriksaan'] = !empty($value->jenispemeriksaanlab_nama) ? $value->jenispemeriksaanlab_nama : '';

                    // Plus the counter
                    $counter++;
                }
            }
            
            // File path
            $filePath = DocoHelpers::exportExcel('Jenis Pemeriksaan Lab', $data, $header, array("uploadPath" => "./uploads"));

            // Return
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }*/

    public function actionExportExcel()
    {
        $title = 'Master Jenis Pemeriksaan Lab';
        try {
            $request = Yii::$app->request;
            $searchKode = '';
            $searchkelompok = '';
            $searchjenis = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['jenispemeriksaanlab_kode'])) {
                    $searchKode = $advancedFilters['jenispemeriksaanlab_kode'];
                }

                if (!empty($advancedFilters['jenispemeriksaanlab_nama'])) {
                    $searchkelompok = $advancedFilters['jenispemeriksaanlab_nama'];
                }
            }

            $model = new JenisPemeriksaanLab;
            $query = $model::find()->joinWith(['kelompokPemeriksaanLab' => function($query) {
                $query->select(['kelompokpemeriksaanlab_m.nama_kelompok', 'kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id']);
            }])->where([JenisPemeriksaanLab::tableName().'.is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                            'kelompokpemeriksaanlab_m.nama_kelompok' => SORT_ASC
                            ]);
            $getData = $query->all();
            $data = [];
            if (!empty($getData)) {
                $counter = 0;
                foreach ($getData as $index => $value) {
                    $data[$counter]['Kode'] = !empty($value->jenispemeriksaanlab_kode) ? $value->jenispemeriksaanlab_kode : '-';
                    $data[$counter]['Kelompok Pemeriksaan'] = !empty($value->kelompokPemeriksaanLab->nama_kelompok) ? $value->kelompokPemeriksaanLab->nama_kelompok : '-';
                    $data[$counter]['Jenis Pemeriksaan'] = !empty($value->jenispemeriksaanlab_nama) ? $value->jenispemeriksaanlab_nama : '-';
                    $counter++;
                    if (!empty($advancedFilters['kelompokpemeriksaanlab_id'])) {
                        $searchkelompok = !empty($value->kelompokPemeriksaanLab->nama_kelompok) ? $value->kelompokPemeriksaanLab->nama_kelompok : '';
                    }
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Kode' => $searchKode,
                'Kelompok Pemeriksaan' => $searchkelompok,
                'Jenis Pemeriksaan' => $searchjenis,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s')
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGenerateApi()
    {
        // jenis pemeriksaaan lab 
        $modelJenisPemeriksaan = new JenisPemeriksaanLab;
        $queryJenisPemeriksaan = $modelJenisPemeriksaan::find()->joinWith(['kelompokPemeriksaanLab' => function($queryJenisPemeriksaan) {
            $queryJenisPemeriksaan->select(['kelompokpemeriksaanlab_m.nama_kelompok', 'kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id']);
        }])->where([JenisPemeriksaanLab::tableName().'.is_deleted' => false]);

        // kelompok pemeriksaan
        $modelKelompokPemeriksaan = new KelompokPemeriksaanLab;
        $queryKelompokPemeriksaan = $modelKelompokPemeriksaan::find()->where(['is_deleted' => false]);

        return [
            'jenis-pemeriksaan' => $queryJenisPemeriksaan->all(),
            'kelompok-pemeriksaan' => $queryKelompokPemeriksaan->all()
        ];
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $getData = JenisPemeriksaanLab::find()
            ->where(['jenispemeriksaanlab_id' => $id])->all();
        
        $getValueKeys = [];
        foreach ($getData as $key => $value) {
            $getValueKeys[] = $value['jenispemeriksaanlab_id'];
        }

        $checkPemeriksaanLab = PemeriksaanLab::find()
            ->where([
                'IN', 'jenispemeriksaanlab_id', $getValueKeys
            ])->all();


        if (empty($checkPemeriksaanLab)) {
            $delete = (new JenisPemeriksaanLab)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Hapus Gagal !',
            'text' => 'Data sudah ini dipakai di master lain '
        ];
    }

    public function actionGetJenisPemeriksaanLab($id)
    {
        // Try catch
        try {
            // Model
            $model = JenisPemeriksaanLab::find()->where(['kelompokpemeriksaanlab_id' => $id])->all();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
?>