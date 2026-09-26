<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-19 11:26:55
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 22:39:28
 */


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

use app\modules\v1\models\LaporanPemeriksaanLabView;
use app\modules\v1\models\PegawaiView;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\PemeriksaanLab;

class LapPemeriksaanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPemeriksaanLabView';
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
        try {
            $model = new LaporanPemeriksaanLabView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($advancedFilter['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $advancedFilter['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($advancedFilter['tglmasukpenunjang']);
                }
            }
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
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
            $title = "Laporan Pemeriksaan Lab";
            $model = new LaporanPemeriksaanLabView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = date('d-M-Y');
            $endString = date('d-M-Y');
            $startLahir = '';
            $endLahir = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        $startString = date('d F Y', strtotime($explode[0]));
                        $endString = date('d F Y', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }   

            }
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
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
                $newValue = [];
                    $newValue[\Yii::t('app', 'Tanggal masuk')] = date('d M Y', strtotime($value['tglmasukpenunjang']));
                    $newValue[\Yii::t('app', 'No Pendaftaran')] = $value['no_pendaftaran'];
                    $newValue[\Yii::t('app', 'No Rekam Medis')] = $value['no_rekam_medik'];
                    $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
                    $newValue[\Yii::t('app', 'Nama dokter')] = $value['dokter'];
                    $newValue[\Yii::t('app', 'Kelompok pemeriksaan')] = $value['nama_kelompok'];
                    $newValue[\Yii::t('app', 'Jenis pemeriksaan')] = $value['jenispemeriksaanlab_nama'];
                    $newValue[\Yii::t('app', 'Nama pemeriksaan')] = $value['daftartindakan_nama'];
                    $newValue[\Yii::t('app', 'Harga satuan (Rp.)')] = $value['tarif_satuan'];
                    $newValue[\Yii::t('app', 'Qty')] = $value['qty_tindakan'];
                    $newValue[\Yii::t('app', 'Cyto (Rp.)')] = $value['tarifcyto_tindakan'];
                    $newValue[\Yii::t('app', 'Total (Rp.)')] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty_tindakan'];
                $result[$key] = $newValue;
                if(!in_array($value['nama_kelompok'], $arrKelompok)){
                    array_push($arrKelompok, $value['nama_kelompok']);
                }
                if(!in_array($value['jenispemeriksaanlab_nama'], $arrJenis)){
                    array_push($arrJenis, $value['jenispemeriksaanlab_nama']);
                }
                if(!in_array($value['daftartindakan_nama'], $arrPemeriksaan)){
                    array_push($arrPemeriksaan, $value['daftartindakan_nama']);
                }
                $qty += $value['qty_tindakan'];
                $totalsatuan += $value['tarif_satuan'];
                $cyto += $value['tarifcyto_tindakan'];
                $total += ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty_tindakan'];
            }
            $footer = [
                'title'=>['Jumlah', 5],
                'data'=>[
                    \Yii::t('app', 'Kelompok pemeriksaan')=>count($arrKelompok),
                    \Yii::t('app', 'Jenis pemeriksaan')=>count($arrJenis),
                    \Yii::t('app', 'Nama pemeriksaan')=>count($arrPemeriksaan),
                    \Yii::t('app', 'Harga satuan (Rp.)') => $totalsatuan,
                    \Yii::t('app', 'Qty')=>$qty,
                    \Yii::t('app', 'Cyto (Rp.)') => $cyto,
                    \Yii::t('app', 'Total (Rp.)') => $total,
                ]
            ];
            $header = ['Periode'=>$periode];

            $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
                "uploadPath" => "./uploads",
            ), $footer,[],true);
            
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
    * @attribute #table_laporan# => menampilkan data laporan pemeriksaan lab
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
            $model = new LaporanPemeriksaanLabView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = date('d-M-Y');
            $endString = date('d-M-Y');
            $startLahir = '';
            $endLahir = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        $startString = date('d F Y', strtotime($explode[0]));
                        $endString = date('d F Y', strtotime($explode[1]));
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
            $getKepalaRuangan = PegawaiView::find()->where(['jabatan_id'=>DocoConstants::VAR_J_K_R, 'ruangan_id'=>$ruangan_id])->one();
            $kepala_ruangan = $getKepalaRuangan['nama_pegawai'];
            $print = new DocoPrint();
            $hidePriceExt = $this->isHidePriceColumn();
            $viewPdfFile = $hidePriceExt['view_pdf_file'];
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial($viewPdfFile, ['data'=>$data]),
                '#periode#' => $periode, 
                '#kepala_ruangan#' => $kepala_ruangan,
                '#tgl_skrg#' => date('d F Y'),
                '#username#' => 'superadmin',
                '#tgl_cetak#' => date('Y-m-d H:i:s'),
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

    public function getDataLaporanExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanPemeriksaanLabView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
            }   
        }
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        $hidePriceExt = $this->isHidePriceColumn();
        $hidePriceColumn = $hidePriceExt['hide_column'];
        $limit = 100;
        $countData = $this->getDataLaporanExcel()->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPemeriksaanLabExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'hide_price_column' => $hidePriceColumn
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportPemeriksaanLab' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'hide_price_column' => $hidePriceColumn
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanPemeriksaanLabExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
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
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Pemeriksaan Laboratorium.xlsx';

        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $kelompokpemeriksaanlab_id = isset($payload['kelompokpemeriksaanlab_id']) ? $payload['kelompokpemeriksaanlab_id'] : null;
        $jenispemeriksaanlab_id = isset($payload['jenispemeriksaanlab_id']) ? $payload['jenispemeriksaanlab_id'] : null;
        $result = [];
        if($type == 'dokter') {
            $result = Pegawai::find()
                ->select(['pegawai_id AS id', 'nama_pegawai AS text'])
                ->where(['kelompokpegawai_id' => 1, 'is_active' => true, 'is_deleted' => false]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
            }

            $result->orderBy(['nama_pegawai' => SORT_ASC]);
        }
        elseif($type == 'kelompok') {
            $result = KelompokPemeriksaanLab::find()
                ->select(['kelompokpemeriksaanlab_id AS id', 'nama_kelompok AS text'])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(nama_kelompok)', strtolower($term)]);
            }

            $result->orderBy(['nama_kelompok' => SORT_ASC]);
        }
        elseif($type == 'jenis_pemeriksaan') {
            $result = JenisPemeriksaanLab::find()
                ->select(['jenispemeriksaanlab_id AS id', 'jenispemeriksaanlab_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($kelompokpemeriksaanlab_id)) {
                $result->andWhere(['kelompokpemeriksaanlab_id' => $kelompokpemeriksaanlab_id]);
            }

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(jenispemeriksaanlab_nama)', strtolower($term)]);
            }

            $result->orderBy(['jenispemeriksaanlab_nama' => SORT_ASC]);
        }
        elseif ($type == 'ruangan') {
            $result = LaporanPemeriksaanLabView::find()
                ->distinct(true)
                ->select(['ruangan_id AS id', 'ruangan_nama AS text']);
        }
        else {
            $result = PemeriksaanLab::find()
                ->select(['daftartindakan_id AS id', 'pemeriksaanlab_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($jenispemeriksaanlab_id)) {
                $result->andWhere(['jenispemeriksaanlab_id' => $jenispemeriksaanlab_id]);
            }

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(pemeriksaanlab_nama)', strtolower($term)]);
            }

            $result->orderBy(['pemeriksaanlab_nama' => SORT_ASC]);
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    protected function isHidePriceColumn()
    {
        return Yii::$app->docoPlugin->execute('lap_pemeriksaan_lab_export');
    }
}