<?php

/**
 * @Author: rizal
 * @Date:   2018-01-24 10:39:52
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRumahSakitView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;

class LapKunjunganRumahSakitController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\LaporanKunjunganRumahSakitView';

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

    private function Model(){
        $model = new LaporanKunjunganRumahSakitView;
        return $model::find();
    }

    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @Update Iqbal
    * @date 2018-08-09
    * @desc
    * @return json list data
    */
    public function actionIndex()
    {
        $model = new LaporanKunjunganRumahSakitView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $start = $advancedFilters['tgl_pendaftaran_awal'];
            $end = $advancedFilters['tgl_pendaftaran_akhir'];
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        if (isset($advancedFilters['list_ruangan_id'])) {
            $arr_ruangan_id = explode(',', $advancedFilters['list_ruangan_id']);
            $query->andWhere(['in', 'ruangan_id', $arr_ruangan_id]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => untuk menampilkan periode data
    * @attribute #tanggal# => untuk menampilkan tanggal sekarang
    * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
    * @attribute #jenis# => untuk menampilkan title
    * @attribute #cetak_oleh# => untuk menampilkan pencetak
    * @attribute #kepala# => untuk menampilkan nama kepala ruangan
    * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Laporan Pendaftaran Kunjungan Rumah Sakit';
        if ($request->get()) {
            $get = $request->get();

            $model = new LaporanKunjunganRumahSakitView;
            $query = $this->model();
            $advancedFilters = $request->get('advanced-filter', []);
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_pendaftaran_awal'])
                        && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                    $start = $advancedFilters['tgl_pendaftaran_awal'];
                    $end = $advancedFilters['tgl_pendaftaran_akhir'];
                }

                if (isset($advancedFilters['ruangan_id'])) {
                    $arr_ruangan_id = explode(',', $advancedFilters['ruangan_id']);
                    $query->andWhere(['in', 'ruangan_id', $arr_ruangan_id]);
                }
            }
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end])
                  ->orderby(['tgl_pendaftaran'=> SORT_DESC]);
            $result = [];
            $tgl_awal = '';
            $tgl_akhir = '';
            foreach ($query->asArray()->all() as $key => $value) {
                $result[] = $value;
            }
            // echo "<pre>";var_dump(count($advancedFilters['ruangan_id']) == 1);die();
            $periode = date('d F Y',strtotime($start)).' - '.date('d F Y',strtotime($end));
            // Yii::$app->jwt->ruangan_id
            $ruangan = count($advancedFilters['ruangan_ids']) == 1 ? $advancedFilters['ruangan_ids'] : Yii::$app->jwt->ruangan_id;
            $dataKepala = (PegawaiView::find()->where([
                'ruangan_id'=>1,
                 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id'=>$ruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->asArray()->one() : '';
            $print = new DocoPrint();
            $namaPegawai = isset($dataKepala['nama_pegawai']) ? $dataKepala['nama_pegawai'] : '-';
            $nomorindukpegawai = isset($dataKepala['nomorindukpegawai']) ? $dataKepala['nomorindukpegawai'] : '-';
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                    'periode' => $periode,
                ]),
                '#periode#' => $periode,
                '#tanggal#' => date('d-F-Y'),
                '#tanggal_cetak#' => date('d F Y'),
                '#jenis#'=>$title,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#kepala#'=> $namaPegawai,
                '#kepalanip#'=> $nomorindukpegawai,
            ];
            $print->Output();
        }
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

    private function getDataExcel()
    {
        $model = new LaporanKunjunganRumahSakitView;
        $query = $model::find();

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $start = $advancedFilters['tgl_pendaftaran_awal'];
            $end = $advancedFilters['tgl_pendaftaran_akhir'];
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        if (isset($advancedFilters['ruangan_id'])) {
            $arr_ruangan_id = explode(',', $advancedFilters['ruangan_id']);
            $query->andWhere(['in', 'ruangan_id', $arr_ruangan_id]);
        }

        if (isset($advancedFilters['no_pendaftaran'])) {
            $query->andWhere(['no_pendaftaran' => $advancedFilters['no_pendaftaran']]);
        }
        return $query;
    }

    public function actionExportExcelBgproses()
    {
        $request = Yii::$app->request;
        $getData = $request->get();

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $countData = $this->getDataExcel($getData)->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'countData' => $countData,
            'sendToUrl' => 'lap-kunjungan-rumah-sakit/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'laporan_kunjungan_rumah_sakit',  'import_data_laporan_kunjungan_rumah_sakit');

        return [
            'totalPerPage' => $countData,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost)
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/' . $no_request . '.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

}
