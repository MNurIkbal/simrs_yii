<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-12 11:00
 * @Last Modified by:   Sigit
 * @Description: controller untuk master Rujukan Keluar Pasien (Pendaftaran)
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\RujukanKeluar;

class RujukanKeluarController extends \Doco\components\DocoActiveController
{

	public $modelClass = 'app\modules\v1\models\RujukanKeluar';

	public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        $model = new RujukanKeluar;
        $query = $model::find()
            ->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama','asalrujukan_m.asalrujukan_id']);
            }])->where(['asalrujukan_m.is_deleted' => 'f']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
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
            $model = new RujukanKeluar;
            $query = $model::find()->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama', 'asalrujukan_m.asalrujukan_id']);
            }])->where(['asalrujukan_m.is_deleted' => 'f']);

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
            $model = new RujukanKeluar;
            $query = $model::find()->joinWith(['asalRujukan' => function($query){
                $query->select(['asalrujukan_m.asalrujukan_nama', 'asalrujukan_m.asalrujukan_id']);
            }])->where(['asalrujukan_m.is_deleted' => 'f']);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Assign data
            if (!empty($query)) {
                // Declare counter
                $counter = 0;

                // Loop
                foreach ($query as $index => $value) {
                    // Assign data
                    $data[$counter]['asal_rujukan'] = !empty($value->asalRujukan) ? $value->asalRujukan->asalrujukan_nama : '-';
                    $data[$counter]['rumah_sakit_rujukan'] = $value->rumahsakit_rujukan;
                    $data[$counter]['alamat_lengkap'] = $value->alamat_rsrujukan;
                    $data[$counter]['no_telp'] = $value->telp_fax;
                    $data[$counter]['status'] = $value->is_active == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');

                    // Plus the counter
                    $counter++;
                }
            }

            // File path
            $filePath = DocoHelpers::exportExcel('Rujukan Keluar', $data, $header, [], [], [], true);

            // Return
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
