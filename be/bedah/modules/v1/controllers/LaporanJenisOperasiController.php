<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model

use app\modules\v1\models\LaporanPemeriksaanOperasi;
use app\modules\v1\models\Operasi;

class LaporanJenisOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPemeriksaanOperasi';
    public $konfig_farmasi;

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
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionGetAttributes()
    {
        $operasi = Operasi::find()->select([
            'operasi_id',
            'operasi_nama'
        ])->where([
            'is_active' => true
        ])->all();
        
        return [
            'operasi' => $operasi,
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanPemeriksaanOperasi;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_tindakan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_tindakan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_tindakan']);
                }   

            }
            $query->andWhere(['between', 'tgl_tindakan', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();
            $request = Yii::$app->request;
            $model = new LaporanPemeriksaanOperasi;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $header = [];

            $header['Tanggal Operasi'] = date('d-M-Y') . ' s/d ' . date('d-M-Y');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($_GET['advanced-filter']['tgl_tindakan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_tindakan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    $header['Tanggal Operasi'] = $_GET['advanced-filter']['tgl_tindakan'];
                    unset($_GET['advanced-filter']['tgl_tindakan']); 
                }

            }

            $query->andWhere(['between', 'tgl_tindakan', $start, $end]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    if (isset($_GET['advanced-filter']['operasi_id'])) {
                        $operasi = $_GET['advanced-filter']['operasi_id'];
                        if ($operasi == $value->operasi_id) {
                            $header['Nama Operasi'] = $value->operasi_nama;
                        }
                    }

                    $data[$counter]['Tanggal Operasi'] = date('d-M-Y',strtotime($value->tgl_tindakan));
                    $data[$counter]['No Pendaftaran'] = $value->no_pendaftaran;
                    $data[$counter]['Nama Pasien'] = $value->nama_pasien;
                    $data[$counter]['Nama Operasi'] = $value->operasi_nama;
                    $data[$counter]['Golongan Operasi'] = $value->golonganoperasi_nama;
                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel('Laporan Jenis Operasi', $data, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * @controller actionExportPdf
     * @attribute #table_laporan# => Menampilkan Laporan Pasien Bedah Sentral 
     * @attribute #periode# => Periode laporan
     **/

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanPemeriksaanOperasi;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = $endString = date('d F Y');
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_tindakan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_tindakan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        $startString = date('d F Y', strtotime($explode[0]));
                        $endString = date('d F Y', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_tindakan']); 
                }   

            }

            $query->andWhere(['between', 'tgl_tindakan', $start, $end]);

            $periode = $startString.' - '.$endString;
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $print = new DocoPrint();
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial('index',['data'=>$data]),
                '#periode#' => $periode
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}