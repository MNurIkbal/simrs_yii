<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPasienSudahBayarView;
use app\modules\v1\models\RincianTagihanPasienSudahBayar;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\RincianTagihanHeader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoPasienSudahBayarView;
use app\modules\v1\models\PembayaranPelayanan;
use app\modules\v1\models\Pembayaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\JenisNonTunai;
use app\modules\v1\models\Penjamin;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;

class LapPasienSudahBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPasienSudahBayarView';

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        // unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienSudahBayarView;
        $query = $model::find();
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $inStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $inEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pendaftaran', $inStart, $inEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            
            if (isset($_GET['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $request->get('advanced-filter')['nama_pasien'];
                $query->andFilterWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $penjamin_id = explode(",",$penjamin_id);
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
        }

            $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);

        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataDetailTindakan($id="")
    {
      $model = new RincianTagihanPasienSudahBayar;
      $query = $model::find()
            ->where(['tandabuktibayar_id'=>$id])
            ->andWhere(['NOT IN','instalasi_id',[6,4,5]]);

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
          'query' => $query,
      ]);
    }

    public function actionDataDetailObat($id="")
    {
      $model = new RincianTagihanPasienSudahBayar;
      $query = $model::find(true)->where(['tandabuktibayar_id'=>$id,'is_obat'=>true]);

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
          'query' => $query,
      ]);
    }

    public function actionDataDetailLab($id="")
    {
      $model = new RincianTagihanPasienSudahBayar;
      $query = $model::find(true)->where(['tandabuktibayar_id'=>$id,'instalasi_id'=>4]);

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
          'query' => $query,
      ]);
    }

    public function actionDataDetailRadiologi($id="")
    {
      $model = new RincianTagihanPasienSudahBayar;
      $query = $model::find(true)->where(['tandabuktibayar_id'=>$id,'instalasi_id'=>5]);

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
          'query' => $query,
      ]);
    }

    public function actionGetPembayaran()
    {
        $model = new InfoPasienSudahBayarView;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetailPembayaran($id='')
    {
        $query =  RincianTagihanPasienSudahBayar::find()->where(['tandabuktibayar_id' => $id]);
        $header = Yii::$app->db->createCommand("
            SELECT * FROM rinciantagihansudahbayarheader_v WHERE tandabuktibayar_id = {$id}
        ")->queryOne();
        return [
            'detail' => $query->asArray()->all(),
            'header' => $header
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_rincian# => table
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienSudahBayarView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
            }
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $caraBayarId = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $caraBayarId]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjaminId = $_GET['advanced-filter']['penjamin_nama'];
                $penjaminId = explode(",",$penjaminId);
                $query->andWhere(['penjamin_id' => $penjaminId]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
        }

        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $print = new DocoPrint();
        $print->attributes = [
            '#table_rincian#' => $this->renderPartial('print_pdf',['data'=>$query->asArray()->all()]),
            '#tgl_pembayaran#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
        ];
        $print->Output();
    }

    /**
    * @controller actionPrintKwitansi
    * @attribute #no_kwitansi# => no kwitansi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    * @attribute #jumlah_diterima# => total pembayaran
    * @attribute #kasir# => kasir
    * @attribute #diterima_dari# => diterima dari
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #jenis_kwitansi# => nama jenis_kwitansi
    * @attribute #terbilang# => terbilang
    **/
    public function actionPrintKwitansi()
    {
        return Yii::$app->docoPlugin->execute('cetak_kwitansi');
    }

    /**
    * @controller actionPrintBkm
    * @attribute #no_bkm# => no bkm
    * @attribute #nama_pasien# => nama pasien
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    * @attribute #jumlah_diterima# => total pembayaran
    * @attribute #kasir# => kasir
    * @attribute #instalasi_nama# => instalasi nama
    * @attribute #terbilang# => terbilang
    **/
    public function actionPrintBkm()
    {
        try{
            $model = new CetakKwitansiBkm;

            $request = Yii::$app->request;
            $id = $request->post('id',null);
            $pdf_id = $request->post('pdf_id',null);

            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['tandabuktibayar_id'=>$id,'pendaftaran_id'=>$pdf_id])->one();

            $print = new DocoPrint();
            $print->attributes = [
              '#no_bkm#'=>isset($data->no_bkm) ? $data->no_bkm : '',
              '#nama_pasien#' => isset($data->nama_pasien) ? $data->nama_pasien : '',
              '#no_pendaftaran#'=>isset($data->no_pendaftaran) ? $data->no_pendaftaran : '',
              '#tgl_pendaftaran#'=> isset($data->tgl_pendaftaran) ? date('d-m-Y', strtotime($data->tgl_pendaftaran)) : '',
              '#tgl_pembayaran#'=>isset($data->tgl_pembayaran) ? date('d-m-Y', strtotime($data->tgl_pembayaran)) : '',
              '#jumlah_diterima#'=> isset($data->total_terbayar)? 'Rp. '.number_format($data->total_terbayar, 0, ',','.') :'',
              '#kasir#'=>isset($data->kasir) ? $data->kasir : '',
              '#instalasi_nama#'=>isset($data->instalasi_nama) ? $data->instalasi_nama : '',
              '#terbilang#' => isset($data->total_terbayar) ? parent::Terbilang($data->total_terbayar). 'Rupiah' : '',
            ];
            $print->Output();
        }catch (\Exception $e){

        }
    }

    protected $_title = "Laporan Pasien Sudah Bayar";
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPasienSudahBayarView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $listPenjamin = $caraBayar = '';
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
            }
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $caraBayarId = $_GET['advanced-filter']['carabayar_nama'];
                $dataCaraBayar = CaraBayar::findOne($caraBayarId);
                $caraBayar = ArrayHelper::getValue($dataCaraBayar, 'carabayar_nama');
                $query->andWhere(['carabayar_id' => $caraBayarId]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjaminId = $_GET['advanced-filter']['penjamin_nama'];
                $penjaminId = explode(",",$penjaminId);
                $dataPenjamin = Penjamin::find()->select(['penjamin_id', 'penjamin_nama'])->where(['penjamin_id' => $penjaminId])->all();
                if(!empty($dataPenjamin)) {
                    foreach ($dataPenjamin as $key => $value) {
                        $listPenjamin[] = ArrayHelper::getValue($value, 'penjamin_nama');
                    }
                    $listPenjamin = implode(', ', $listPenjamin);
                }
                $query->andWhere(['penjamin_id' => $penjaminId]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
        }

        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
            $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
            $totalTagihan = $totalTagihan + $biayaAdministrasi;
            $totalDijamin = ArrayHelper::getValue($value, 'subsidi_asuransi', 0);
            $totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
            $totalDibayar = $totalTagihan - $totalDijamin - $totalDiskon;

            $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
            $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
            $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
            $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
            $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
            $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
            $tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
            $noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');

            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pembayaran')] = date('d M Y H:i:s', strtotime($tglPembayaran));
            $newValue[\Yii::t('app', 'Instalasi - Ruangan Akhir')] = $instalasiNama. ' - '.$ruanganNama;
            $newValue[\Yii::t('app', 'No Pendaftaran')] = $noPendaftaran;
            $newValue[\Yii::t('app', 'Nama Pasien')] = $namaPasien. ' - '.$noRekamMedik;
            $newValue[\Yii::t('app', 'Cara Bayar - Penjamin')] = $caraBayarNama. ' - '.$penjaminNama;
            $newValue[\Yii::t("app", "Jumlah Tagihan")] = number_format($totalTagihan, 2);
            $newValue[\Yii::t('app', 'Jumlah Dijamin')] = number_format($totalDijamin, 2);
            $newValue[\Yii::t('app', 'Diskon')] = number_format($totalDiskon, 2);
            $newValue[\Yii::t("app", "Total Pembayaran")] = number_format($totalDibayar, 2);
            $result[$key] = $newValue;
        }
        // Directory Creation
        $header = array(
            Yii::t("app", "Tanggal Pembayaran") => ((date('d M Y',strtotime($start))." - ".date('d M Y', strtotime($end)))),
            Yii::t("app", "Nama Pasien") => (@$_GET['advanced-filter']['nama_pasien']),
            Yii::t("app", "Cara Bayar") => $caraBayar,
            Yii::t("app", "Penjamin") => $listPenjamin,
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ),[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetApi()
    {
        $result['carabayar'] = [];
        $result['penjamin'] = [];
        try{
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = $result['carabayar']['response'];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = $result['penjamin']['response'];
            return $result;
        } catch(\Exception $e){
            return $result;
        }
    }

    public function actionExportPdfBgProses()
    {
        $kode_doc = 'print-lap-pasien-sudah-bayar';
        $request = Yii::$app->request;
        $get = $request->get();
        $postData = $request->post();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;
        $data = $this->getData();
        $countData = count($data->asArray()->all());
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'DataLaporanPasienSudahBayar' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'params' => $get,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLaporanPasienSudahBayar' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'kode_doc' => $kode_doc,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanPasienSudahBayar' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    protected function getDataPdf($params = []){
        $request = Yii::$app->request;
        $model = new InfoPasienSudahBayarView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $_GET = !empty($params) ? $params : $request->get();
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
            }
            
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $inStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $inEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pendaftaran', $inStart, $inEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $caraBayarId = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $caraBayarId]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjaminId = $_GET['advanced-filter']['penjamin_nama'];
                $penjaminId = explode(",",$penjaminId);
                $query->andWhere(['penjamin_id' => $penjaminId]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
        }

        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDownloadFilePdf()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.pdf';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $carabayarId = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;
        $result = [];
        if($type == 'carabayar') {
            $result = CaraBayar::find()
                ->select(['carabayar_id AS id', 'carabayar_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
            }
        }
        else {
            $result = Penjamin::find()
                ->select(['penjamin_id AS id', 'penjamin_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($carabayarId)) {
                $result->andWhere(['carabayar_id' => $carabayarId]);
            }

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
            }
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    
    public function actionExportExcelBgprocess() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $headerExcel = [];
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        /** set header excel */

        $start = date('d M Y');
        $end = date('d M Y');

        $nama_pasien = $caraBayar = $listPenjamin = $startOut = $endOut = $startIn = $endIn ='';
        
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pembayaran'])) {
                $tgl_pembayaran = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_pembayaran']);
                $start = !empty($tgl_pembayaran['startDate']) ?  date('d M Y', strtotime($tgl_pembayaran['startDate'])) : '';
                $end = !empty($tgl_pembayaran['endDate']) ?  date('d M Y', strtotime($tgl_pembayaran['endDate'])) : '';
            }  

            if(isset($get['advanced-filter']['tgl_pulang'])) {
                $tgl_pulang = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_pulang']);
                $startOut = !empty($tgl_pulang['startDate']) ?  date('d M Y', strtotime($tgl_pulang['startDate'])) : '';
                $endOut = !empty($tgl_pulang['endDate']) ?  date('d M Y', strtotime($tgl_pulang['endDate'])) : '';
            }

            if(isset($get['advanced-filter']['tgl_pendaftaran'])) {
                $tgl_pendaftaran = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_pendaftaran']);
                $startIn = !empty($tgl_pendaftaran['startDate']) ?  date('d M Y', strtotime($tgl_pendaftaran['startDate'])) : '';
                $endIn = !empty($tgl_pendaftaran['endDate']) ?  date('d M Y', strtotime($tgl_pendaftaran['endDate'])) : '';
            }
            
            if(isset($get['advanced-filter']['carabayar_nama'])) {
                $caraBayarId = $get['advanced-filter']['carabayar_nama'];
                $dataCaraBayar = CaraBayar::findOne($caraBayarId);
                $caraBayar = ArrayHelper::getValue($dataCaraBayar, 'carabayar_nama');
            }
            if(isset($get['advanced-filter']['penjamin_nama'])) {
                $penjaminId = $get['advanced-filter']['penjamin_nama'];
                $penjaminId = explode(",",$penjaminId);
                $dataPenjamin = Penjamin::find()->select(['penjamin_id', 'penjamin_nama'])->where(['penjamin_id' => $penjaminId])->all();
                if(!empty($dataPenjamin)) {
                    foreach ($dataPenjamin as $key => $value) {
                        $listPenjamin[] = ArrayHelper::getValue($value, 'penjamin_nama');
                    }
                    $listPenjamin = implode(', ', $listPenjamin);
                }
            }
            if(isset($get['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $get['advanced-filter']['nama_pasien'];
            }
        }
        
        $headerExcel = [
            "Tanggal Pembayaran" => $start . ' - ' . $end,
            "Tanggal Masuk" => $startIn . ' - ' . $endIn,
            "Tanggal Pulang" => $startOut . ' - ' . $endOut,
            "Nama Pasien" => $nama_pasien,
            "Cara Bayar" => $caraBayar,
            "Penjamin" => $listPenjamin,
        ];
        
        $data = $this->getData();
        $countData = count($data->asArray()->all());
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'lap-pasien-sudah-bayar/drop-file',
            'base_uri' => $uri_kasir,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcelSudahBayar' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Laporan Pasien Sudah Bayar',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'customData' => true,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getData(){
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new InfoPasienSudahBayarView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pembayaran'])) {
                $tgl_pembayaran = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_pembayaran']);
                $start = !empty($tgl_pembayaran['startDate']) ?  $tgl_pembayaran['startDate'] : '';
                $end = !empty($tgl_pembayaran['endDate']) ?  $tgl_pembayaran['endDate'] : '';
                
                unset($get['advanced-filter']['tgl_pembayaran']);
            }

            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $inStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $inEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tgl_pendaftaran', $inStart, $inEnd]);
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

        }
        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
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
        $fileName = $dir.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName, $dir);
    }

    public function actionDataLapPasienSudahBayar(){
        $request = Yii::$app->request;
        $_GET = $request->get('params', null);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
        }
        $data = $this->getDataPdf($_GET);
        $attributes = [
            '#table_rincian#' => $this->renderPartial('print_pdf',['data'=>$data->asArray()->all()]),
            '#tgl_pembayaran#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
        ];
        return $attributes;
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }
}
