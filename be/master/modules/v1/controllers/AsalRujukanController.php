<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-10 13:34:49
 * @Last Modified by:   Sigit
 * @Description: controller untuk master Asal Rujukan Pasien (Pendaftaran)
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\AsalRujukan;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;


class AsalRujukanController extends \Doco\components\DocoActiveController
{
   	public $modelClass = 'app\modules\v1\models\AsalRujukan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
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

        $model = new AsalRujukan;
        $query = $model::find()
             ->select([
                                'asalrujukan_m.asalrujukan_id',
                                'asalrujukan_m.asalrujukan_nama',
                                'asalrujukan_m.asalrujukan_namalainnya',
                                'asalrujukan_m.asalrujukan_institusi',
                                'asalrujukan_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListAsalRujukan()
    {
        $data = AsalRujukan::find()->where(['is_active' => 't'])->orderBy('asalrujukan_id');
        $items = ArrayHelper::map($data->all(), 'asalrujukan_id', 'asalrujukan_nama');

        return $items;
    }

    public function actionCheckAsalRujukan($nama, $id = null){

      $CheckAsalRujukan = AsalRujukan::find()->where(['asalrujukan_nama' => $nama, 'is_deleted' => false]);
      if ($id) {
        $CheckAsalRujukan->andWhere(['not in','asalrujukan_id',[$id]]);
      }
      return $CheckAsalRujukan->asArray()->one();;
    }

    /*public function actionCheckPerujuk($id=null)
    {
        $query = (new \yii\db\Query())
                ->select('*')
                ->from('perujuk_m')
                ->where(['asalrujukan_id' => $id, 'is_deleted' => false])
                ->all();

        return $query;
    }*/

    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table
    **/
    public function actionExportPdf()
    {
        // Get pemeriksaan fisik
        // $model = AsalRujukan::find()->all();
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'asalrujukan_m');
        
        $model = new AsalRujukan;
        $query = $model::find()
             ->select([
                                'asalrujukan_m.asalrujukan_id',
                                'asalrujukan_m.asalrujukan_nama',
                                'asalrujukan_m.asalrujukan_namalainnya',
                                'asalrujukan_m.asalrujukan_institusi',
                                'asalrujukan_m.is_active',
                      ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query = $query->all();

        // Check model
        if (!empty($query)) {
            // Header
            $header = array(
                Yii::t('app', "Asal Rujukan") => 'Asal Rujukan',
            );

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
    }

    // Export excel
    public function actionExportExcel()
    {
        try {
            $title = Yii::t('app', 'Master Asal Rujukan');
            $header = array();
            $footer = array();
            $model = new AsalRujukan;
            $query = $model->find()->select(['asalrujukan_nama','asalrujukan_kode','asalrujukan_institusi','asalrujukan_namalainnya','is_active']);

            $asalrujukan_nama = '-';
            $asalrujukan_kode = '-';
            $asalrujukan_institusi = '-';
            $asalrujukan_namalainnya = '-';
            $status = '-';

            if (isset($_GET['advanced-filter']['asalrujukan_nama'])) {
                $asalrujukan_nama = $_GET['advanced-filter']['asalrujukan_nama'];
            }

            if (isset($_GET['advanced-filter']['asalrujukan_kode'])) {
                $asalrujukan_kode = $_GET['advanced-filter']['asalrujukan_kode'];
            }

            if (isset($_GET['advanced-filter']['asalrujukan_institusi'])) {
                $asalrujukan_institusi = $_GET['advanced-filter']['asalrujukan_institusi'];
            }

            if (isset($_GET['advanced-filter']['asalrujukan_namalainnya'])) {
                $asalrujukan_namalainnya = $_GET['advanced-filter']['asalrujukan_namalainnya'];
            }

            if (isset($_GET['advanced-filter']['is_active'])) {
                $status = $_GET['advanced-filter']['is_active'] ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');
            }

            $header = array(
                Yii::t('app', "Asal Rujukan") => $asalrujukan_nama,
                Yii::t('app', "Asal Rujukan Kode") => $asalrujukan_kode,
                Yii::t('app', "Institusi Asal Rujukan") =>  $asalrujukan_institusi,
                Yii::t('app', "Nama Lainnya") => $asalrujukan_namalainnya,
                Yii::t('app', "Status") => $status,
            );

            $model = DocoRestActiveFilter::advancedFilter($model, $query);
            $model = $model->asArray()->all();

            $filePath = DocoHelpers::exportExcel($title, $model, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
