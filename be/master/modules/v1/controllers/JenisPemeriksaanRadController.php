<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\PemeriksaanRad;

class JenisPemeriksaanRadController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\JenisPemeriksaanRad';

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
        unset($actions['delete']);

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
            $_GET['expand'] = $request->get('expand', 'kelompokpemeriksaanrad_m');

            // Define model
            $model = new JenisPemeriksaanRad;

            // Query
            $query = $model::find()->joinWith(['kelompokPemeriksaanRad' => function($query) {
                // Select
                $query->select(['kelompokpemeriksaanrad_m.nama_kelompok', 'kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id']);
            }])->where([JenisPemeriksaanRad::tableName().'.is_deleted' => false]);

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

    // Action get kelompok by jenis id
    public function actionGetKelompokByJenisId($id)
    {
        // Try catch
        try {
            // Define model
            $model = JenisPemeriksaanRad::findOne($id);

            // Check kelompok
            if ($model->kelompokPemeriksaanRad) {
                // Return
                return $model->kelompokPemeriksaanRad;
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
            $model = new JenisPemeriksaanRad;
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
            $model = new JenisPemeriksaanRad;
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
                    $data[$counter]['Kode'] = !empty($value->jenispemeriksaanrad_kode)
                        ? $value->jenispemeriksaanrad_kode
                        : '';
                    $data[$counter]['Kelompok pemeriksaan'] = !empty($value->kelompokPemeriksaanRad->nama_kelompok)
                    ? $value->kelompokPemeriksaanRad->nama_kelompok
                    : '';
                    $data[$counter]['Jenis pemeriksaan'] = !empty($value->jenispemeriksaanrad_nama)
                        ? $value->jenispemeriksaanrad_nama
                        : '';

                    // Plus the counter
                    $counter++;
                }
            }

            // File path
            $filePath = DocoHelpers::exportExcel('Jenis Pemeriksaan Radiologi', $data, $header, array("uploadPath" => "./uploads"));

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
        // jenis pemeriksaaan rad 
        $modelJenisPemeriksaan = new JenisPemeriksaanRad;
        $queryJenisPemeriksaan = $modelJenisPemeriksaan::find()->joinWith(['kelompokPemeriksaanrad' => function($queryJenisPemeriksaan) {
            $queryJenisPemeriksaan->select(['kelompokpemeriksaanrad_m.nama_kelompok', 'kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id']);
        }])->where([JenisPemeriksaanRad::tableName().'.is_deleted' => false]);

        // kelompok pemeriksaan
        $modelKelompokPemeriksaan = new KelompokPemeriksaanRad;
        $queryKelompokPemeriksaan = $modelKelompokPemeriksaan::find()->where(['is_deleted' => false]);

        return [
            'jenis-pemeriksaan' => $queryJenisPemeriksaan->all(),
            'kelompok-pemeriksaan' => $queryKelompokPemeriksaan->all()
        ];
    }

    public function actionDelete($id)
    {
        try {
            $model = JenisPemeriksaanRad::findOne($id);
            $data = PemeriksaanRad::find()->where(['jenispemeriksaanrad_id' => $id, 'kelompokpemeriksaanrad_id' => $model->kelompokpemeriksaanrad_id])->one();
            if (!empty($data)) {
                \Yii::$app->response->statusCode = 500;
                $result = [
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data sudah di gunakan'
                ];
            } else {
                $result = (new JenisPemeriksaanRad)->delete($id);
            }

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetJenisPemeriksaanRad($id)
    {
        // Try catch
        try {
            // Model
            $model = JenisPemeriksaanRad::find()->where(['kelompokpemeriksaanrad_id' => $id])->all();

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