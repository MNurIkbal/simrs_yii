<?php

/**
 * @author Randy Vianda Putra
 * @todo Laporan pemeriksaan radiologi
 * @copyright 31 Juli 2018 aweutist
 */


namespace app\modules\v1\controllers;

use app\components\DocoDatatableHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model

use app\modules\v1\models\LaporanPemeriksaanRadView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\Daftartindakan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\UploadForm;
use Doco\Services\InternalService;
use yii\web\Response;
use yii\web\UploadedFile;

class LapPemeriksaanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPemeriksaanRadView';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-display-antrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
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

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanPemeriksaanRadView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_periksa'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_periksa']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_periksa']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['status_contrast'])) {
                    $query->andWhere(['status_contrast' => $_GET['advanced-filter']['status_contrast']]);
                }
            }
            // if ($between) {
            // }
            $query->andWhere(['between', 'tgl_periksa', $start, $end]);
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
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $title = "Laporan Pemeriksaan Radiologi";
            $model = new LaporanPemeriksaanRadView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = '';
            $endString = '';
            $startLahir = '';
            $endLahir = '';
            $header = [];
            $periode = date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end));
            $header['Tanggal Masuk'] = $periode;
            $advancedFilter = [];
            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($_GET['advanced-filter']['tgl_verifikasi'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_verifikasi']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_verifikasi']);
                    $periode = date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end));
                    $header['Tanggal Masuk'] = $periode;
                }
                if (isset($advancedFilter['no_pendaftaran'])) {
                        $no_pendaftaran = $advancedFilter['no_pendaftaran'];
                        $header['No Pendaftaran'] = $no_pendaftaran;
                    }

                if (isset($advancedFilter['no_rekam_medik'])) {
                    $no_rekam_medik = $advancedFilter['no_rekam_medik'];
                    $header['No Rekam Medis'] = $no_rekam_medik;
                }

                if (isset($advancedFilter['kelaspelayanan_id'])) {
                    $kelaspelayananid = $advancedFilter['kelaspelayanan_id'];
                    $data_filter = KelasPelayanan::find()->where(['kelaspelayanan_id' => $kelaspelayananid])->one();
                    $header['Kelas Pelayanan'] = $data_filter->kelaspelayanan_nama;
                }

                if (isset($advancedFilter['nama_pasien'])) {
                    $nama_pasien = $advancedFilter['nama_pasien'];
                    $header['Nama Pasien'] = $nama_pasien;
                }

                if (isset($advancedFilter['pegawai_id'])) {
                    $dokter = $advancedFilter['pegawai_id'];
                    $header['Dokter'] = $dokter;
                }

                if (isset($advancedFilter['kelompokpemeriksaanrad_id'])) {
                    $kelompokpemeriksaanrad_id = $advancedFilter['kelompokpemeriksaanrad_id'];
                    $data_filter = KelompokPemeriksaanRad::find()->where(['kelompokpemeriksaanrad_id' => $kelompokpemeriksaanrad_id])->one();
                    $header['Kelompok Pemeriksaan'] = $data_filter->nama_kelompok;
                }

                if (isset($advancedFilter['jenispemeriksaanrad_id'])) {
                    $jenispemeriksaanrad_id = $advancedFilter['jenispemeriksaanrad_id'];
                    $data_filter = JenisPemeriksaanRad::find()->where(['jenispemeriksaanrad_id' => $jenispemeriksaanrad_id])->one();
                    $header['Jenis Pemeriksaan'] = $data_filter->jenispemeriksaanrad_nama;
                }

                if (isset($advancedFilter['daftartindakan_id'])) {
                    $daftartindakan_id = $advancedFilter['daftartindakan_id'];
                    $data_filter = Daftartindakan::find()->where(['daftartindakan_id' => $daftartindakan_id])->one();
                    $header['Pemeriksaan'] = $data_filter->daftartindakan_nama;
                }
            }

            $query->andWhere(['between', 'tgl_verifikasi', $start, $end]);
            
            $periode = $startString.' - '.$endString;
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $arrKelompok = [];
            $arrJenis = [];
            $arrPemeriksaan = [];
            $totalsatuan = 0;
            $qty = 0;
            $cyto = 0;
            $total = 0;
            foreach ($data as $key => $value) {
                $tarif_satuan = ArrayHelper::getValue($value, 'tarif_satuan', 0);
                $tarifcyto_tindakan = ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0);
                $qty_tindakan = ArrayHelper::getValue($value, 'qty_tindakan', 0);
                $nama_kelompok = ArrayHelper::getValue($value, 'nama_kelompok', '');
                $jenispemeriksaanrad_nama = ArrayHelper::getValue($value, 'jenispemeriksaanrad_nama', '');
                $daftartindakan_nama = ArrayHelper::getValue($value, 'daftartindakan_nama', '');
                $dokter = ArrayHelper::getValue($value, 'dokter', '');
                $newValue = [];
                    $newValue[\Yii::t('app', 'Tanggal Verifikasi')] = !empty($value['tgl_verifikasi']) ? date('d M Y', strtotime($value['tglmasukpenunjang'])) : '';
                    $newValue[\Yii::t('app', 'No Pendaftaran')] = ArrayHelper::getValue($value, 'no_pendaftaran', '');
                    $newValue[\Yii::t('app', 'No Rekam Medis')] = ArrayHelper::getValue($value, 'no_rekam_medik', '');
                    $newValue[\Yii::t('app', 'Nama Pasien')] = ArrayHelper::getValue($value, 'nama_pasien', '');
                    $newValue[\Yii::t('app', 'Nama dokter')] = $dokter;
                    $newValue[\Yii::t('app', 'Nama Dokter Perujuk')] = ArrayHelper::getValue($value, 'dokter_perujuk_nama', '');
                    $newValue[\Yii::t('app', 'Kelas Pelayanan')] = ArrayHelper::getValue($value, 'kelaspelayanan_nama', '');
                    $newValue[\Yii::t('app', 'Kelompok pemeriksaan')] = $nama_kelompok;
                    $newValue[\Yii::t('app', 'Jenis pemeriksaan')] = $jenispemeriksaanrad_nama;
                    $newValue[\Yii::t('app', 'Contrast')] = ArrayHelper::getValue($value, 'status_contrast', '');
                    $newValue[\Yii::t('app', 'Nama pemeriksaan')] = ArrayHelper::getValue($value, 'daftartindakan_nama', '');
                    $newValue[\Yii::t('app', 'Harga satuan (Rp.)')] = ArrayHelper::getValue($value, 'tarif_satuan', '');
                    $newValue[\Yii::t('app', 'Qty')] = ArrayHelper::getValue($value, 'qty_tindakan', '');
                    $newValue[\Yii::t('app', 'Cyto (Rp.)')] = ArrayHelper::getValue($value, 'tarifcyto_tindakan', '');
                    $newValue[\Yii::t('app', 'Total (Rp.)')] = ($tarif_satuan+$tarifcyto_tindakan)*$qty_tindakan;
                $result[$key] = $newValue;
                if(!in_array($nama_kelompok, $arrKelompok)){
                    array_push($arrKelompok, $nama_kelompok);
                }
                if(!in_array($jenispemeriksaanrad_nama, $arrJenis)){
                    array_push($arrJenis, $jenispemeriksaanrad_nama);
                }
                if(!in_array($daftartindakan_nama, $arrPemeriksaan)){
                    array_push($arrPemeriksaan, $daftartindakan_nama);
                }
                if (isset($advancedFilter['pegawai_id'])) {
                    $header['Dokter'] = $dokter;
                }
                $qty += $qty_tindakan;
                $totalsatuan += $tarif_satuan ;
                $cyto += $tarifcyto_tindakan;
                $total += ($tarif_satuan +$tarifcyto_tindakan)*$qty_tindakan;
            }
            $footer = [
                'title'=>['Jumlah', 6],
                'data'=>[
                    \Yii::t('app', 'Kelompok pemeriksaan')=>count($arrKelompok),
                    \Yii::t('app', 'Jenis pemeriksaan')=>count($arrJenis),
                    \Yii::t('app', 'Nama pemeriksaan')=>count($arrPemeriksaan),
                    \Yii::t('app', 'Qty')=>$qty,
                    \Yii::t('app', 'Harga satuan (Rp.)')=> $totalsatuan,
                    \Yii::t('app', 'Cyto (Rp.)') => $cyto,
                    \Yii::t('app', 'Total (Rp.)')=> $total,
                ]
            ];
            $filePath = DocoHelpers::exportExcel($title, $result, $header, array("uploadPath" => "./uploads"),$footer,[],true);

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
    * @attribute #table_laporan# => menampilkan data laporan pemeriksaan rad
    * @attribute #periode# => menampilkan periode laporan
    * @attribute #kepala_ruangan# => menampilkan kepala ruangan
    * @attribute #tgl_skrg# => menampilkan tanggal hari ini
    * @attribute #username# => menampilkan nama pengguna
    * @attribute #tgl_cetak# => menampilkan tanggal cetak laporan
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanPemeriksaanRadView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = date('d M Y');
            $endString = date('d M Y');
            $startLahir = '';
            $endLahir = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        $startString = date('d M Y', strtotime($explode[0]));
                        $endString = date('d M Y', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            
            $periode = $startString.' - '.$endString;
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : '';
            $getKepalaRuangan = PegawaiView::find()->where(['jabatan_id' => DocoConstants::VAR_J_K_R, 'ruangan_id' => $ruangan_id])->one();
            $kepala_ruangan = ArrayHelper::getValue($getKepalaRuangan, 'nama_pegawai', '');
            $print = new DocoPrint();
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial('index', ['data' => $data]),
                '#periode#' => $periode,
                '#kepala_ruangan#' => $kepala_ruangan,
                '#tgl_skrg#' => date('d F Y'),
                '#username#' => 'superadmin',
                '#tgl_cetak#' => date('d M Y H:i:s'),
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

    public function actionUnduhFile()
    {
        $req  = Yii::$app->request;
        $get  = $req->get();
        $tipe = ArrayHelper::getValue($get, 'tipe', 1);

        $auth = $req->getHeaders()->get('Authorization');
        $xOwner = $req->getHeaders()->get('X-Owner');
        $randString = ArrayHelper::getValue($get, 'randString');

        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);

        $data = $this->actionGetObjectData();
        $countData = ($tipe == 1) ? count($data) : ArrayHelper::getValue($data, 'countData', 0);
        $totalPerPage = ceil($countData / 20);
        $url = Yii::$app->docoRest->getBaseUri('radiologi');
        $params = [
            'sendToUrl' => 'lap-pemeriksaan/drop-file',
            'getDataUrl' => 'lap-pemeriksaan/get-object-data',
            'base_uri' => $url,
        ];
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPemeriksaanRad' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'filter' => $get,
                'params' => $params,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLaporanPemeriksaanRad' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'totalPerPage' => $totalPerPage,
                'countData' => $countData,
                'filter' => $get,
                'title' => 'Laporan Pemeriksaan Radiologi',
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanPemeriksaanRad' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'totalPerPage' => $totalPerPage,
                'params' => $params,
                'tipe' => $tipe
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetObjectData()
    {
        $req  = Yii::$app->request;
        $get  = $req->get();
        $tipe = ArrayHelper::getValue($get, 'tipe', 1);
        $data = $this->getData()->asArray()->all();

        if($tipe == 2) {
            $startRujukan = date('Y-m-d 00:00:00');
            $endRujukan = date('Y-m-d 23:59:59');

            if(isset($get['advanced-filter']['tgl_periksa'])) {
                $tglRujukan = $get['advanced-filter']['tgl_periksa'];
                $explode = explode(" - ", $tglRujukan);
                if(count($explode) == 2) {
                    $startRujukan = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endRujukan = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }

            $periode = date('d M Y', strtotime($startRujukan)).' - '.date('d M Y', strtotime($endRujukan));
            $attributes = [
                '#datatable#' => $this->renderPartial('pdf', [
                    'data' => $data,
                ]),
                '#periode#' => $periode,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#tanggal#' => date('d M Y H:i'),
            ];
            $data = [
                'periode' => $periode,
                'attributes' => $attributes,
                'countData' => count($data),
            ];
        }

        return $data;
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
        $filename = $request->get('filename', null);
        $tipe = $request->get('tipe', 1);
        $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
        $rootPath = './uploads';
        $files = $rootPath.'/'.$filename . $ext;
        if(file_exists($files)) {
            header('Content-Description: File Transfer');
            if($tipe == 1) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
            else {
                header('Content-Type: application/pdf');
            }
            header("Content-Disposition: inline; filename=$files");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($files);
            unlink($files);
            die();
        }
    }

    private function getData()
    {
        $req  = Yii::$app->request;
        $get  = $req->get();
        $model = new LaporanPemeriksaanRadView;
        $query = $model::find();

        $startRujukan     = date('Y-m-d 00:00:00');
        $endRujukan       = date('Y-m-d 23:59:00');
        $startPersetujuan = null;
        $endPersetujuan   = null;
    
        if(isset($get['advanced-filter'])) {
            $advancedFilter = $get['advanced-filter'];

            if(isset($advancedFilter['tgl_periksa'])) {
                $tglRujukan = $advancedFilter['tgl_periksa'];
                $explode = explode(" - ", $tglRujukan);
                if(count($explode) == 2) {
                    $startRujukan = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endRujukan = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($get['advanced-filter']['tgl_periksa']);
            }

            if(isset($advancedFilter['status_contrast'])) {
                $statusContrast = $advancedFilter['status_contrast'];
                $query->andWhere(['status_contrast' => $statusContrast]);
                
                unset($get['advanced-filter']['status_contrast']);
            }
    
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
        }
    
        $query->andWhere(['between', 'tgl_periksa', $startRujukan, $endRujukan]); 

        return $query;
    }

    public function actionKelasPelayanan(){
        $model = new KelasPelayanan;
        $query = $model::find();

        return $query->asArray()->all();
    }
}