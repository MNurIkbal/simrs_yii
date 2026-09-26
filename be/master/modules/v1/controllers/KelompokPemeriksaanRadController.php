<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kelompok Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\PemeriksaanRad;
use app\modules\v1\models\JenisPemeriksaanRad;

class KelompokPemeriksaanRadController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\KelompokPemeriksaanRad';

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
        // var_dump($actions);
        // exit;

        // Return actions
        return $actions;
    }

    public function actionDelete($id)
    {
        try {
            $data = JenisPemeriksaanRad::find()->where(['kelompokpemeriksaanrad_id' => $id])->one();
            if (!empty($data)) {
                \Yii::$app->response->statusCode = 500;
                $result = [
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data sudah di gunakan'
                ];
            } else {
                \Yii::$app->response->statusCode = 200;
                $result = (new KelompokPemeriksaanRad)->delete($id);
            }

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action index
    public function actionIndex()
    {
        // Try catch
        try {
            // Define model
            $model = new KelompokPemeriksaanRad;
            $query = $model::find()->where(['is_deleted' => false]);

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
            $model = new KelompokPemeriksaanRad;
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
            // Declare empty variables
            $data = array();
            $header = array();

            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new KelompokPemeriksaanRad;
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
                    $data[$counter]['kode'] = $value->kode_kelompok;
                    $data[$counter]['kelompok_pemeriksaan'] = $value->nama_kelompok;
                    // Plus the counter
                    $counter++;
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

            $filePath = DocoHelpers::exportExcel('Kelompok Pemeriksaan Radiologi', $data, $header, [],$footer,[],true);
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

    public function actionGetKelompokPemeriksaanRad($id)
    {
        try {
            $sql = 'SELECT t.*
                FROM
                    kelompokpemeriksaanrad_m t
                JOIN jenispemeriksaanrad_m r ON r.kelompokpemeriksaanrad_id = t.kelompokpemeriksaanrad_id
                WHERE r.jenispemeriksaanrad_id = '.$id.'
            ';

            $result = KelompokPemeriksaanRad::findBySql($sql)->all();

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