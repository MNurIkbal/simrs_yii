<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\InfoPemberianPiutangView;
use app\modules\v1\models\PembayaranPiutang;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\TandaBuktiBayar;
use Doco\models\ProfilRsView;
use app\modules\v1\models\PemberianPiutang;
use Doco\models\kasir\Pembayaran;
use Doco\Services\InternalService;
use yii\helpers\ArrayHelper;

class InformasiPemberianPiutangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemberianPiutangView';
    protected static $range = '';

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

    public function actionIndex()
    {
        $model = new InfoPemberianPiutangView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $end = date('Y-m-d 23:59:00', strtotime('+1 months'));
        
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_pemberianpiutang'])) {
                $explode = explode(" - ", $advancedFilters['tgl_pemberianpiutang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pemberianpiutang']);
            }

            if(isset($advancedFilters['tglpasienpulang'])) {
                $explode = explode(" - ", $advancedFilters['tglpasienpulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tglpasienpulang', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }

            if (isset($advancedFilters['pegawaidibebankan_id'])) {
                $pegawaidibebankan_id = $advancedFilters['pegawaidibebankan_id'];
                $query->andWhere(['pegawaidibebankan_id' => $pegawaidibebankan_id]);
                unset($_GET['advanced-filter']['pegawaidibebankan_id']);
            }

            if (isset($advancedFilters['status_piutang'])) {
                $status_piutang = $advancedFilters['status_piutang'];
                $query->andWhere(['status_piutang' => $status_piutang]);
                unset($_GET['advanced-filter']['status_piutang']);
            }

        }
        
        $query->andWhere(['between', 'tgl_pemberianpiutang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $model = new InfoPemberianPiutangView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $end = date('Y-m-d 23:59:00');
        $title = 'Informasi Pemberian Piutang';

        $nama_pasien = $no_rekam_medik = $no_pendaftaran = $status_piutang_nama = $pegawaidibebankan_nama = '-';

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_pemberianpiutang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pemberianpiutang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pemberianpiutang']);
            }

            if (isset($advancedFilter['pegawaidibebankan_nama'])) {
                $query->andWhere(['pegawaidibebankan_id' => $advancedFilter['pegawaidibebankan_nama']]);
                $data = Yii::$app->db->createCommand('select nama_pegawai, nomorindukpegawai from pegawai_m where pegawai_id = ' . $advancedFilter['pegawaidibebankan_nama'])->queryOne();
                $pegawaidibebankan_nama = $data['nomorindukpegawai'] . '-' . $data['nama_pegawai'];
                unset($_GET['advanced-filter']['pegawaidibebankan_nama']);
            }

            if (isset($advancedFilter['status_piutang_nama'])) {
                $status_piutang_nama = ($advancedFilter['status_piutang_nama'] == DocoConstants::LUNAS) ? "Lunas" : "BELUM LUNAS";
                $query->andWhere(['status_piutang' => $advancedFilter['status_piutang_nama']]);
                unset($_GET['advanced-filter']['status_piutang_nama']);
            }

            if (isset($advancedFilter['nama_pasien'])) {
                $nama_pasien = $advancedFilter['nama_pasien'];
            }

            if (isset($advancedFilter['no_rekam_medik'])) {
                $no_rekam_medik = $advancedFilter['no_rekam_medik'];
            }

            if (isset($advancedFilter['no_pendaftaran'])) {
                $no_pendaftaran = $advancedFilter['no_pendaftaran'];
            }
        }

        $query->andWhere(['between', 'tgl_pemberianpiutang', $start, $end]);
        $data = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        foreach ($data as $key => $value) {
            $value['tgl_pemberianpiutang'] = date("j M Y", strtotime($value['tgl_pemberianpiutang']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pemberian Piutang')] = $value['tgl_pemberianpiutang'];
            $newValue[\Yii::t('app', 'Nama pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Nama Karyawan')] = $value['pegawaidibebankan_nip'] . '-' . $value['pegawaidibebankan_nama'];
            $newValue[\Yii::t('app', 'No rekam medis')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'No pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t("app", "Piutang (Rp.)")] = $value['total_piutang'];
            $newValue[\Yii::t('app', 'Sudah Dibayar (Rp.)')] = $value['total_bayarpiutang'];
            $newValue[\Yii::t('app', 'Sisa Piutang (Rp.)')] = $value['total_sisapiutang'];
            $newValue[\Yii::t('app', 'Status')] = $value['status_piutang_nama'];
            $newValue[''] = '';
            $result[$key] = $newValue;
        }

        $header = [
            Yii::t("app", "Periode") => $start . ' - ' . $end,
            Yii::t("app", "Nama Pasien") => $nama_pasien,
            Yii::t("app", "No Rekam Medik") =>  $no_rekam_medik,
            Yii::t("app", "Nama Karyawan") => $pegawaidibebankan_nama,
            Yii::t("app", "No Pendaftaran") => $no_pendaftaran,
            Yii::t("app", "Status") => $status_piutang_nama,
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ), [], [], true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetDataPiutang($id)
    {
        $query = InfoPemberianPiutangView::find()->where(['pemberianpiutang_id' => $id]);

        return $query->one();
    }

    public function actionSavePembayaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if($post) {
                $model = new PembayaranPiutang;
                unset($post['total_sisapiutang']);
                $model->attributes = $post;
                $model->metode_pembayaran = $post['metode_pembayaran'];
                if($model->validate() && $model->save()) {
                    $idParent = $model->pembayaranpiutang_id;
                    $tandaBuktiBayar = new TandaBuktiBayar;
                    $tandaBuktiBayar->pegawai1_id = Yii::$app->user->identity->loginpemakai_id;
                    $tandaBuktiBayar->ruangan_id = Yii::$app->jwt->ruangan_id;
                    $tandaBuktiBayar->tglbuktibayar = date('Y-m-d H:i:s');
                    $tandaBuktiBayar->jmlpembayaran = $post['total_bayarpiutang'];
                    $tandaBuktiBayar->uangditerima = 0;
                    $tandaBuktiBayar->pembayaranpiutang_id = $idParent;
                    $tandaBuktiBayar->carapembayaran = '-';
                    $tandaBuktiBayar->jmlpembulatan = 0;
                    $tandaBuktiBayar->uangkembalian = 0;
                    if(!$tandaBuktiBayar->validate()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $tandaBuktiBayar->errors
                        ]);
                    }

                    $tandaBuktiBayar->save();
                    $transaction->commit();
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                }
                else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $transaction->callBack();
            return [
                'message' => $e->getMessage()
            ];
        }
        
    }

    /**
    * @controller actionCetakKwitansi
    * @attribute #no_transaksi# => no transaksi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #dari_kepada# => dari kepada
    * @attribute #tgl_pembayaran# => tanggal transaksi
    * @attribute #tgl_pembayaran2# => tanggal transaksi
    * @attribute #tanggal_sekarang# => tanggal sekarang
    * @attribute #jumlah# => total pembayaran
    * @attribute #nama_pegawai# => kasir
    * @attribute #terbilang# => terbilang
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #printed_by# => printed_by
    * @attribute #alamatRs# => kota
    **/
    public function actionCetakKwitansi()
    {
        try{
            $request = Yii::$app->request;
            $id = $request->get('id',null);
            $nama_pegawai = $request->get('nama_pegawai',null);
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }
            $data = Yii::$app->db->createCommand("
                SELECT * FROM infopemberianpiutang_v 
                WHERE pemberianpiutang_id = {$id}
            ")->queryOne();
            $created_date = isset($data['created_date']) ? date('d-m-Y H:i:s', strtotime($data['created_date'])) : '';
            $created_date2 = isset($data['created_date']) ? $this->helper->convertDate($data['created_date']) : '';
            $exp = explode('-', $created_date2);
            $date = isset($exp[0]) ? $exp[0] : '';
            $month = isset($exp[1]) ? $exp[1] : '';
            $year = isset($exp[2]) ? $exp[2] : '';
            $created_date2 = $date.' '.$month.' '.$year;
            $profileRs = $this->getProfileRs();
            $namaRs = isset($profileRs['namaRs']) ? $profileRs['namaRs'] : '';
            $kota = isset($profileRs['kota']) ? $profileRs['kota'] : '';
            $print = new DocoPrint('kwt-pemberian-piutang');
            $print->attributes = [
              '#no_transaksi#'=>isset($data['no_pemberianpiutang']) ? $data['no_pemberianpiutang'] : '',
              '#nama_pasien#' => isset($data['nama_pasien']) ? $data['nama_pasien'] : '',
              '#dari_kepada#' => isset($data['nama_pasien']) ? $data['nama_pasien'] : '',
              '#tgl_pembayaran#'=> $created_date,
              '#tgl_pembayaran2#'=> $created_date2,
              '#jumlah#'=> isset($data['total_piutang'])? number_format($data['total_piutang'], 0, ',','.') :'',
              '#nama_pegawai#'=>isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '',
              '#terbilang#' => isset($data['total_piutang']) ? DocoHelpers::Terbilang($data['total_piutang']). 'Rupiah' : '',
              '#tanggal_sekarang#'=> date('d M Y'),
              '#no_rekam_medik#'=>isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '',
              '#no_pendaftaran#'=>isset($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '',
              '#keterangan#'=>isset($data['catatan']) ? $data['catatan'] : '',
              '#printed_by#' => $nama_pegawai,
              '#alamatRs#' => $kota
            ];
            $print->Output();
        }catch (\Exception $e){

        }
    }

    protected function getProfileRs()
    {
        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });
        $kota = '-';
        $namaRs = '-';
        if (!empty($profilRs['nama_rumahsakit'])) {
          $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
          if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
              $pattern = "KOTA ADM. ";
          }
          elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
              $pattern = "KAB. ADM. ";
          }
          elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
              $pattern = "KAB. ";
          }
          elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
              $pattern = "KOTA ";
          }
          elseif($match = preg_match("/Kota /i", $profilRs['kota'])) {
              $pattern = "Kota ";
          }

          $kota = str_replace($pattern,"", $profilRs['kota']);
        }
          
        return [
          'namaRs' => $namaRs,
          'kota' => $kota,
          'alamat' => $profilRs['alamatlokasi_rumahsakit'],
          'no_telp' => $profilRs['no_telp_profilrs'],
        ];
    }

    /**
     * @Author: [Bambang Hermawan][bambang.hermawan@sirs.co.id]
     * 
     * BG PROSES - DOWNLOAD EXCEL INFORMASI PEMBERIAN PIUTANG
     * 
     * --------------------------------------------------
     */

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();

        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataLaporanExcel($getData)->asArray()->all();
        $countData = count($data);
    
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'InformasiPemberianPiutangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportInformasiPemberianPiutangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadInformasiPemberianPiutangExcel' => [
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

    private function getDataLaporanExcel($filter)
    {
        $model = new InfoPemberianPiutangView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
        $end = date('Y-m-d 23:59:00');

        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_pemberianpiutang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pemberianpiutang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pemberianpiutang']);
            }

            if(isset($filter['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tglpasienpulang']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                $query->andWhere(['between', 'tglpasienpulang', $outStart, $outEnd]);
                unset($filter['advanced-filter']['tglpasienpulang']);
            }
            
            if (isset($filter['advanced-filter']['pegawaidibebankan_id'])) {
                $pegawaidibebankan_id = $filter['advanced-filter']['pegawaidibebankan_id'];
                $query->andWhere(['pegawaidibebankan_id' => $pegawaidibebankan_id]);
                unset($_GET['advanced-filter']['pegawaidibebankan_id']);
            }

            if (isset($filter['advanced-filter']['status_piutang'])) {
                $status_piutang = $filter['advanced-filter']['status_piutang'];
                $query->andWhere(['status_piutang' => $status_piutang]);
                unset($_GET['advanced-filter']['status_piutang']);
            }

        }

        $query->andWhere(['between', 'tgl_pemberianpiutang', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;

            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path . '/' . $model->file;
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

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '/Informasi Pemberian Piutang.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionBatal()
    {
        $request = Yii::$app->request;
        $id = $request->post('id', null);
        
        $alasan_batal = $request->post('alasan_batal', null);
        $password = $request->post('password', null);
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $check = $jwt->katakunci_pemakai;
        $valid = Yii::$app->security->validatePassword($password, $check);
        if(!$valid) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal',
                'text' => 'User/password tidak valid'
            ];
        }
        try {
            $data = PemberianPiutang::findOne($id);
            $pembayaran = [];
            $pendaftaran_id = ArrayHelper::getValue($data, 'pendaftaran_id', null);
            if(!empty($pendaftaran_id)) {
                $pembayaran = Pembayaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->exists();
            }
            
            if ($pembayaran) {
                $result = [
                    'status' => 422,
                    'title' => 'Proses Gagal',
                    'text' => 'Piutang tersebut sudah digunakan untuk pembayaran tagihan. Tidak bisa dibatalkan! <br />Silahkan batalkan pembayaran tagihan terlebih dahulu.'
                ];
            } else {
                $totalBayarPiutang = ArrayHelper::getValue($data, 'total_bayarpiutang', 0);
                if($totalBayarPiutang > 0) {
                    $result = [
                        'status' => 422,
                        'title' => 'Proses Gagal',
                        'text' => 'Tidak Bisa Membatalkan Piutang, Sudah Melakukan Pembayaran Piutang.'
                    ];
                }
                else {
                    $user = Yii::$app->jwt->user;
                    $now = date('Y-m-d H:i:s');
                    $data->is_deleted = true;
                    $data->deleted_date = $now;
                    $data->deleted_by = $user->loginpemakai_id;
                    $data->alasan_batal = $alasan_batal;

                    if(!$data->save()) {
                        $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal',
                            'text' => 'Batal Piutang Gagal'
                        ];
                    }
                    else {
                        $result = [
                            'status' => 200,
                            'title' => 'Proses Berhasil',
                            'text' => 'Batal Piutang Berhasil'
                        ];
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}