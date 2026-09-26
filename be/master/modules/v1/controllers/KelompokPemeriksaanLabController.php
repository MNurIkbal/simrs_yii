<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 11:31:32
 * @Last Modified by:   iqbal@docotel
 * @Last Modified time: 2019-02-21 13:36:24
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\PemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\Ruangan;

class KelompokPemeriksaanLabController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelompokPemeriksaanLab';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "GET"];
        // $verbs["delete"] = ["DELETE", "GET"];
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
            $model = new KelompokPemeriksaanLab;
            $query = $model::find()->where(['is_deleted' => false]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['nama_kelompok' => SORT_ASC]);
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
            $model = new KelompokPemeriksaanLab;
            $model->attributes = $request->post();
            if($model->validate()){
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'KelompokPemeriksaanLabForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'KelompokPemeriksaanLabForm');
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
            $model = KelompokPemeriksaanLab::findOne($id);
            if($model->validate()){
                if ($request->post() && !empty($model)) {
                    $model->attributes = $request->post();
                    if ($model->update()) {
                        return [
                            'message' => 'Data Berhasil di ubah',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'KelompokPemeriksaanLabForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'KelompokPemeriksaanLabForm');
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
            $model = new KelompokPemeriksaanLab;
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

    public function actionExportExcel()
    {
        $title = 'Master Kelompok Pemeriksaan Lab';
        try {
            $request = Yii::$app->request;
            $searchKode = '';
            $searchkelompok = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['kode_kelompok'])) {
                    $searchKode = $advancedFilters['kode_kelompok'];
                }

                if (!empty($advancedFilters['nama_kelompok'])) {
                    $searchkelompok = $advancedFilters['nama_kelompok'];
                }
            }

            $model = new KelompokPemeriksaanLab;
            $query = $model::find()->where(['is_deleted' => false]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['nama_kelompok' => SORT_ASC]);
            $getData = $query->all();

            $data = [];
            if (!empty($getData)) {
                $counter = 0;
                foreach ($getData as $index => $value) {
                    $data[$counter]['Kode'] = !empty($value->kode_kelompok) ? $value->kode_kelompok : '-';
                    $data[$counter]['Kelompok Pemeriksaan'] = !empty($value->nama_kelompok) ? $value->nama_kelompok : '-';
                    $counter++;
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Kode' => $searchKode,
                'Kelompok Pemeriksaan' => $searchkelompok,
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

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $getData = KelompokPemeriksaanLab::find()
            ->where(['kelompokpemeriksaanlab_id' => $id])->all();
        
        $getValueKeys = [];
        foreach ($getData as $key => $value) {
            $getValueKeys[] = $value['kelompokpemeriksaanlab_id'];
        }

        $checkJenisPemeriksaan = JenisPemeriksaanLab::find()
            ->where([
                'IN', 'kelompokpemeriksaanlab_id', $getValueKeys
            ])->all();

        $checkPemeriksaanLab = PemeriksaanLab::find()
            ->where([
                'IN', 'kelompokpemeriksaanlab_id', $getValueKeys
            ])->all();


        if (empty($checkJenisPemeriksaan) && empty($checkPemeriksaanLab)) {
            $delete = (new KelompokPemeriksaanLab)->delete($id);
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


    public function actionGetKelompokPemeriksaanLab($id)
    {
        try {
            $sql = 'SELECT t.*
                FROM
                    kelompokpemeriksaanlab_m t
                JOIN jenispemeriksaanlab_m r ON r.kelompokpemeriksaanlab_id = t.kelompokpemeriksaanlab_id
                WHERE r.jenispemeriksaanlab_id = '.$id.'
            ';

            $result = KelompokPemeriksaanLab::findBySql($sql)->all();

            return $result;
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