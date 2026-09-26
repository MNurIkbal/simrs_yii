<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPembayaranTransaksiView;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\models\ProfilRsView;

class InfTransaksiInOutController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPembayaranTransaksiView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $model = new InfoPembayaranTransaksiView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_transaksi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_transaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_transaksi']); // Unset Advanced Filter  date range
                $between = true;
            }

            if (isset($_GET['advanced-filter']['jenis'])) {
                $query->andWhere(['jenis' => $_GET['advanced-filter']['jenis']]);
            }

            if (isset($_GET['advanced-filter']['tipe'])) {
                $query->andWhere(['tipe' => $_GET['advanced-filter']['tipe']]);
            }

            if (isset($_GET['advanced-filter']['metode_pembayaran_nama'])) {
                $query->andWhere(['metode_pembayaran_nama' => $_GET['advanced-filter']['metode_pembayaran_nama']]);
            }
        }

        $query->andWhere(['between', 'tgl_transaksi', $start, $end]);
        // $query->andWhere(['created_by' => $jwt->loginpemakai_id]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetAttributes()
    {
        $getLookup = Lookup::find()->select([
            'lookup_id',
            'lookup_type',
            'lookup_name',
        ])->andWhere([
            'lookup_type' => [
                'metode_bayar',
                'tipe_transaksi',
                'jenis_transaksi',
            ]
        ])->asArray()->all();

        $listAttr = [];
        foreach ($getLookup as $value) {
            $id = isset($value['lookup_id']) ? (int) $value['lookup_id'] : null;
            $type = isset($value['lookup_type']) ? $value['lookup_type'] : null;
            $name = isset($value['lookup_name']) ? $value['lookup_name'] : null;
            $listAttr[$type][$id] = $name;
        }
        return $listAttr;
    }

    public function actionGetLookupType()
    {
        return [
            'metode_bayar' => $this->lookup_type->actionGetLookupType('metode_bayar'),
            'tipe_transaksi' => $this->lookup_type->actionGetLookupType('tipe_transaksi'),
            'jenis_transaksi' => $this->lookup_type->actionGetLookupType('jenis_transaksi'),
        ];
    }

    public function actionListRequest()
    {
        $request = Yii::$app->request;
        $modelLookup = new Lookup;

        // metode_bayar
        $Qmetode_bayar = $modelLookup::find(true);
        $Qmetode_bayar->select(['lookup_id', 'lookup_type', 'lookup_name']);
        $Qmetode_bayar->where(['lookup_type' => 'metode_bayar']);
        $Qmetode_bayar = DocoRestActiveFilter::advancedFilter($modelLookup, $Qmetode_bayar);
        $Qmetode_bayar = new ActiveDataProvider([
            'query' => $Qmetode_bayar,
        ]);

        // tipe_transaksi
        $Qtipe_transaksi = $modelLookup::find(true);
        $Qtipe_transaksi->select(['lookup_id', 'lookup_type', 'lookup_name']);
        $Qtipe_transaksi->where(['lookup_type' => 'tipe_transaksi']);
        $Qtipe_transaksi = DocoRestActiveFilter::advancedFilter($modelLookup, $Qtipe_transaksi);
        $Qtipe_transaksi = new ActiveDataProvider([
            'query' => $Qtipe_transaksi,
        ]);

        // jenis_transaksi
        $Qjenis_transaksi = $modelLookup::find(true);
        $Qjenis_transaksi->select(['lookup_id', 'lookup_type', 'lookup_name']);
        $Qjenis_transaksi->where(['lookup_type' => 'jenis_transaksi']);
        $Qjenis_transaksi = DocoRestActiveFilter::advancedFilter($modelLookup, $Qjenis_transaksi);
        $Qjenis_transaksi = new ActiveDataProvider([
            'query' => $Qjenis_transaksi,
        ]);

        return [
            'metode_bayar' => $Qmetode_bayar->getModels(),
            'tipe_transaksi' => $Qtipe_transaksi->getModels(),
            'jenis_transaksi' => $Qjenis_transaksi->getModels(),
        ];
    }

    protected $_title = "Informasi Transaksi Penerimaan / Pengeluaran";
    public function actionExportExcel()
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $model = new InfoPembayaranTransaksiView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_transaksi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_transaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_transaksi']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->andWhere(['between', 'tgl_transaksi', $start, $end]);
        // $query->andWhere(['created_by' => $jwt->loginpemakai_id]);
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];

        foreach ($query->asArray()->all() as $key => $value) {
            // Data Selection
            $value['tgl_transaksi'] = date("j M Y", strtotime($value['tgl_transaksi']));

            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal')] = $value['tgl_transaksi'];
            $newValue[\Yii::t('app', 'No Transaksi')] = $value['no_transaksi'];
            $newValue[\Yii::t('app', 'Jenis')] = $value['jenis'];
            $newValue[\Yii::t('app', 'Dari / Kepada')] = $value['dari_kepada'];
            $newValue[\Yii::t('app', 'Tipe')] = $value['tipe'];
            $newValue[\Yii::t('app', 'Deskripsi')] = $value['deskripsi'];
            $newValue[\Yii::t("app", "Metode Pembayaran")] = $value['metode_pembayaran_nama'];
            $newValue[\Yii::t("app", "Jumlah (Rp.)")] = $value['jumlah'];
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "Tanggal") => ((date('d-M-Y', strtotime($start))." - ".date('d-M-Y', strtotime($end)))),
            Yii::t("app", "No Transaksi") => (@$_GET['advanced-filter']['no_transaksi']),
            Yii::t("app", "Jenis") => (@$_GET['advanced-filter']['jenis']),
            Yii::t("app", "Dari / Kepada") => (@$_GET['advanced-filter']['dari_kepada']),
            Yii::t("app", "Tipe") => (@$_GET['advanced-filter']['tipe']),
            Yii::t("app", "Metode Pembayaran") => (@$_GET['advanced-filter']['metode_pembayaran_nama']),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionCetakKwitansi
    * @attribute #no_transaksi# => no transaksi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #dari_kepada# => dari kepada
    * @attribute #tgl_transaksi# => tanggal transaksi
    * @attribute #tgl_transaksi2# => tanggal transaksi
    * @attribute #tanggal_sekarang# => tanggal sekarang
    * @attribute #jumlah# => total pembayaran
    * @attribute #created_by_nama# => kasir
    * @attribute #kategoritransaksi_nama#
    * @attribute #terbilang# => terbilang
    * @attribute #jenis# => jenis
    * @attribute #tipe_kode# => tipe_kode
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
                SELECT * FROM infopembayarantransaksi_v 
                WHERE pembayarantransaksi_id = {$id}
            ")->queryOne();
            $jenis = isset($data['jenis_transaksi']) ? $data['jenis_transaksi'] : '';
            $diterimaDari = isset($data['dari_kepada']) ? $data['dari_kepada'] : '';
            $created_by_nama = isset($data['created_by_nama']) ? $data['created_by_nama'] : '';
            $nama_pasien = isset($data['nama_pasien']) ? $data['nama_pasien'] : '';
            $dokter = isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '';
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
            if($jenis == DocoConstants::JENIS_TRANSAKSI_PENGELUARAN) {
                $diterimaDari = $namaRs;
                if (ArrayHelper::getValue($data, 'tipe_transaksi') == DocoConstants::TIPE_TRANSAKSI_KARYAWAN) {
                    $created_by_nama = ArrayHelper::getValue($data, 'nama_pegawai');
                } else if (ArrayHelper::getValue($data, 'tipe_transaksi') == DocoConstants::TIPE_TRANSAKSI_VENDOR) {
                    $created_by_nama = ArrayHelper::getValue($data, 'supplier_nama');
                } else {
                    if(empty($nama_pasien)) {
                        $nama_pasien = $dokter;
                    }
                    $created_by_nama = $nama_pasien;
                }
            }
            $print = new DocoPrint('kwitansi-transaksi');
            $print->attributes = [
              '#jenis#'=>isset($data['jenis']) ? strtoupper($data['jenis']) : '',
              '#no_transaksi#'=>isset($data['no_transaksi']) ? $data['no_transaksi'] : '',
              '#nama_pasien#' => isset($data['nama_pasien']) ? $data['nama_pasien'] : '',
              '#dari_kepada#' => $diterimaDari,
              '#tgl_transaksi#'=> $created_date,
              '#tgl_transaksi2#'=> $created_date2,
              '#jumlah#'=> isset($data['jumlah'])? number_format($data['jumlah'], 0, ',','.') :'',
              '#created_by_nama#'=> $created_by_nama,
              // '#kategoritransaksi_nama#'=>isset($data['kategoritransaksi_nama']) ? $data['kategoritransaksi_nama'] : '',
              '#kategoritransaksi_nama#'=>isset($data['deskripsi']) ? $data['deskripsi'] : '',
              '#terbilang#' => isset($data['jumlah']) ? DocoHelpers::Terbilang($data['jumlah']). 'Rupiah' : '',
              // '#tanggal_sekarang#'=> date('d M Y'),
              '#tanggal_sekarang#'=> isset($data['tgl_transaksi']) ? date_format(date_create($data['tgl_transaksi']), "d M Y") : '',
              '#tipe_kode#'=>isset($data['tipe_kode']) ? $data['tipe_kode'] : '',
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
}