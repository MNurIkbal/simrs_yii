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
use app\modules\v1\models\KegiatanOperasi;
use app\modules\v1\models\Operasi;

class KegiatanOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KegiatanOperasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    // Action index
    public function actionIndex()
    {
        // Try catch
        try {
            
            $model = new KegiatanOperasi;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeleted($id)
    {
        try {
            $data = Operasi::find()->where(['kegiatanoperasi_id' => $id])->one();
            if (!empty($data)) {
                \Yii::$app->response->statusCode = 500;
                $result = [
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data sudah di gunakan'
                ];
            } else {
                \Yii::$app->response->statusCode = 200;
                $result = (new KegiatanOperasi)->delete($id);
            }

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportPdf()
    {
        // Try catch
        try {
            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new KegiatanOperasi;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Check model
            if (!empty($model)) {
               
                $header = array();
                
                
                $print = new DocoPrint();

                
                $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $query,
                    ]),
                ];

                
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
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
            $model = new KegiatanOperasi;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Assign data
            if (!empty($query)) {
                // Declare counter
                $counter = 0;

                
                foreach ($query as $index => $value) {
                    // Assign data
                    $data[$counter]['kode_kelompok'] = $value->kode_kelompok;
                    $data[$counter]['nama_kelompok'] = $value->nama_kelompok;
                    $data[$counter]['keterangan_kelompok'] = $value->keterangan_kelompok;

                    // Plus the counter
                    $counter++;
                }
            }
            
            // File path
            $filePath = DocoHelpers::exportExcel('Kelompok Pemeriksaan Rad', $data, $header, array("uploadPath" => "./uploads"));

            
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
