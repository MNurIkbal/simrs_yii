<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 16:51:32
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-27 17:45:40
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
use app\modules\v1\models\DaftarTindakan;

class PemeriksaanLabController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\PemeriksaanLab';

    // Verbs
    public function verbs()
    {
        // Verbs parent
        $verbs = parent::verbs();

        // Return verbs
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Actions parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);
        unset($actions['view']);

        // Return actions
        return $actions;
    }

    // Action index
    public function actionIndex()
    {
        // Try catch
        try {
            // Get request and expand the get
            $request = Yii::$app->request;
            $_GET['expand'] = $request->get('expand', 'jenispemeriksaanlab_m, kelompokpemeriksaanlab_m, daftartindakan_m');

            // Define model
            $model = new PemeriksaanLab;

            // Query
            $query = $model::find()->joinWith(['jenisPemeriksaanLab' => function($query) {
                // Select
                $query->select([
                    'jenispemeriksaanlab_m.jenispemeriksaanlab_nama', 'jenispemeriksaanlab_m.jenispemeriksaanlab_id'
                ]);
            }])->joinWith(['kelompokPemeriksaanLab' => function($query) {
                // Select
                $query->select(['kelompokpemeriksaanlab_m.nama_kelompok', 'kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id']);
            }])->joinWith(['daftarTindakan' => function($query) {
                // Select
                $query->select(['daftartindakan_m.daftartindakan_nama', 'daftartindakan_m.daftartindakan_id']);
            }])->where([PemeriksaanLab::tableName().'.is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    // Action index
    public function actionGetLastPemeriksaan()
    {
        // Try catch
        try {
            // Define model
            $model = PemeriksaanLab::find()->where([PemeriksaanLab::tableName().'.is_deleted' => false])->andWhere([PemeriksaanLab::tableName().'.is_active' => true])->orderBy([PemeriksaanLab::tableName().'.pemeriksaanlab_id' => SORT_DESC])->limit(1)->one();

            // Return data
            return $model;
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
            $model = new PemeriksaanLab;
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
    public function actionExportExcel()
    {
        // Try catch
        try {
            // Declare emty data
            $data = array();
            $header = array();

            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new PemeriksaanLab;
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
                    $data[$counter]['Kode'] = !empty($value->pemeriksaanlab_kode) ? $value->pemeriksaanlab_kode : '';
                    $data[$counter]['Nama Pemeriksaan'] = !empty($value->daftarTindakan->daftartindakan_nama) ? $value->daftarTindakan->daftartindakan_nama : '';
                    $data[$counter]['Kelompok Pemeriksaan'] = !empty($value->jenisPemeriksaanLab->kelompokPemeriksaanLab->nama_kelompok) ? $value->jenisPemeriksaanLab->kelompokPemeriksaanLab->nama_kelompok : '';
                    $data[$counter]['Jenis Pemeriksaan'] = !empty($value->jenisPemeriksaanLab->jenispemeriksaanlab_nama) ? $value->jenisPemeriksaanLab->jenispemeriksaanlab_nama : '';

                    // Plus the counter
                    $counter++;
                }
            }

            // File path
            $filePath = DocoHelpers::exportExcel('Pemeriksaan Lab', $data, $header, array("uploadPath" => "./uploads"),[],[],true);

            // Return
            $filePath->save('php://output');
            die;
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

    public function actionGenerateApi()
    {
        // daftar tindakan
        $modelTindakan = new DaftarTindakan;
        $queryTindakan = $modelTindakan::find()->where(['kelompoktindakan_id' => DocoConstants::KEL_TIN_LAB]);

        // jenis pemeriksaaan lab 
        $modelJenisPemeriksaan = new JenisPemeriksaanLab;
        $queryJenisPemeriksaan = $modelJenisPemeriksaan::find()->joinWith(['kelompokPemeriksaanLab' => function($queryJenisPemeriksaan) {
            $queryJenisPemeriksaan->select(['kelompokpemeriksaanlab_m.nama_kelompok', 'kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id']);
        }])->where([JenisPemeriksaanLab::tableName().'.is_deleted' => false]);

        // kelompok pemeriksaan
        $modelKelompokPemeriksaan = new KelompokPemeriksaanLab;
        $queryKelompokPemeriksaan = $modelKelompokPemeriksaan::find()->where(['is_deleted' => false]);

        // pemeriksaan lab
        $modelPemeriksaanLab = new PemeriksaanLab;
        $queryPemeriksaanLab = $modelPemeriksaanLab::find()->where(['is_deleted' => false]);

        return [
            'daftar-tindakan' => $queryTindakan->all(),
            'jenis-pemeriksaan' => $queryJenisPemeriksaan->all(),
            'kelompok-pemeriksaan' => $queryKelompokPemeriksaan->all(),
            'pemeriksaan-lab' => $queryPemeriksaanLab->all(),
        ];
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $data = [];
        if (!empty($id)) {
            $query = PemeriksaanLab::find()
                ->with('kelompokPemeriksaanLab')
                ->where(['pemeriksaanlab_id' => $id])
                ->one();
            $data = $query;
        }
        return $data;
    }
}
?>