<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\PemeriksaanRad;
use app\modules\v1\models\DaftarTindakan;

class PemeriksaanRadController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\PemeriksaanRad';

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
            $_GET['expand'] = $request->get('expand', 'jenispemeriksaanrad_m, kelompokpemeriksaanrad_m, daftartindakan_m');

            // Define model
            $model = new PemeriksaanRad;

            // Query
            $query = $model::find()->joinWith(['jenisPemeriksaanRad' => function($query) {
                // Select
                $query->select([
                    'jenispemeriksaanrad_m.jenispemeriksaanrad_nama', 'jenispemeriksaanrad_m.jenispemeriksaanrad_id'
                ]);
            }])->joinWith(['kelompokPemeriksaanRad' => function($query) {
                // Select
                $query->select(['kelompokpemeriksaanrad_m.nama_kelompok', 'kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id']);
            }])->joinWith(['daftarTindakan' => function($query) {
                // Select
                $query->select(['daftartindakan_m.daftartindakan_nama', 'daftartindakan_m.daftartindakan_id']);
            }])->where([PemeriksaanRad::tableName().'.is_deleted' => false]);

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
            $model = PemeriksaanRad::find()->where([PemeriksaanRad::tableName().'.is_deleted' => false])->andWhere([PemeriksaanRad::tableName().'.is_active' => true])->orderBy([PemeriksaanRad::tableName().'.pemeriksaanRad_id' => SORT_DESC])->limit(1)->one();

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
            $model = new PemeriksaanRad;
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
            $model = new PemeriksaanRad;
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
                    $data[$counter]['Kode'] = !empty($value->pemeriksaanrad_kode) ? $value->pemeriksaanrad_kode : '';
                    $data[$counter]['Nama Pemeriksaan'] = !empty($value->daftarTindakan->daftartindakan_nama) ? $value->daftarTindakan->daftartindakan_nama : '';
                    $data[$counter]['Kelompok Pemeriksaan'] = !empty($value->jenisPemeriksaanRad->kelompokPemeriksaanRad->nama_kelompok) ? $value->jenisPemeriksaanRad->kelompokPemeriksaanRad->nama_kelompok : '';
                    $data[$counter]['Jenis Pemeriksaan'] = !empty($value->jenisPemeriksaanRad->jenispemeriksaanrad_nama) ? $value->jenisPemeriksaanRad->jenispemeriksaanrad_nama : '';

                    // Plus the counter
                    $counter++;
                }
            }

            // File path
            $filePath = DocoHelpers::exportExcel('Pemeriksaan Radiologi', $data, $header, array("uploadPath" => "./uploads"));

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
    }

    public function actionGenerateApi()
    {
        // daftar tindakan
        $modelTindakan = new DaftarTindakan;
        $queryTindakan = $modelTindakan::find()->where(['kelompoktindakan_id' => DocoConstants::KEL_TIN_RAD]);

        // jenis pemeriksaaan lab 
        $modelJenisPemeriksaan = new JenisPemeriksaanRad;
        $queryJenisPemeriksaan = $modelJenisPemeriksaan::find()->joinWith(['kelompokPemeriksaanRad' => function($queryJenisPemeriksaan) {
            $queryJenisPemeriksaan->select(['kelompokpemeriksaanrad_m.nama_kelompok', 'kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id']);
        }])->where([JenisPemeriksaanRad::tableName().'.is_deleted' => false]);

        // kelompok pemeriksaan
        $modelKelompokPemeriksaan = new KelompokPemeriksaanRad;
        $queryKelompokPemeriksaan = $modelKelompokPemeriksaan::find()->where(['is_deleted' => false]);

        // pemeriksaan rad
        $modelPemeriksaanRad = new PemeriksaanRad;
        $queryPemeriksaanRad = $modelPemeriksaanRad::find()->where(['is_deleted' => false]);

        return [
            'daftar-tindakan' => $queryTindakan->all(),
            'jenis-pemeriksaan' => $queryJenisPemeriksaan->all(),
            'kelompok-pemeriksaan' => $queryKelompokPemeriksaan->all(),
            'pemeriksaan-rad' => $queryPemeriksaanRad->all(),
        ];
    }
}
?>