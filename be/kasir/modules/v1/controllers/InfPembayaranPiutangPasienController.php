<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoPembayaranPiutang;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\models\ProfilRsView;

class InfPembayaranPiutangPasienController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoPembayaranPiutang';
    protected $_title = "Informasi Pembayaran Piutang Pasien ";

    public function verbs()
    {
        $verbs = parent::verbs();
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
        try {
            $model = new InfoPembayaranPiutang;
            $query = $model::find(true);
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
            **/
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pembayaranpiutang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaranpiutang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pembayaranpiutang']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if(isset($_GET['advanced-filter']['nama_pasien'])){
                    $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                    $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
                }

                if(isset($_GET['advanced-filter']['no_rekam_medik'])){
                    $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                    $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
                }

                if(isset($_GET['advanced-filter']['no_pendaftaran'])){
                    $no_pendaftaran = $_GET['advanced-filter']['no_pendaftaran'];
                    $query->andWhere(['ILIKE', 'no_pendaftaran', $no_pendaftaran]);
                }

                if(isset($_GET['advanced-filter']['no_pembayaranpiutang'])){
                    $no_pembayaranpiutang = $_GET['advanced-filter']['no_pembayaranpiutang'];
                    $query->andWhere(['ILIKE', 'no_pembayaranpiutang', $no_pembayaranpiutang]);
                }

                if(isset($_GET['advanced-filter']['metode_pembayaran'])){
                    $metode_pembayaran = $_GET['advanced-filter']['metode_pembayaran'];
                    $metode_pembayaran = explode("-", $metode_pembayaran);
                    if(count($metode_pembayaran) > 1) {
                        $query->andWhere(['metode_pembayaran' => $metode_pembayaran[0]]);
                        $query->andWhere(['jenisnontunai_id' => $metode_pembayaran[1]]);
                    } else {
                        $query->andWhere(['metode_pembayaran' => $metode_pembayaran[0]]);
                    }
                }
            }        
            $query->andWhere(['between', 'tgl_pembayaranpiutang', $start, $end]); 

            /**
             * End Special Condition date range
            **/
            
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

    public function actionGenerateApiPiutang()
    {
        // InfoPembayaranPiutang
        $modelPiutang = new InfoPembayaranPiutang;
        $queryPiutang = $modelPiutang::find();

        $queryPiutang = DocoRestActiveFilter::advancedFilter($modelPiutang, $queryPiutang);
        $queryPiutang = new ActiveDataProvider([
            'query' => $queryPiutang,
        ]);

        return [
            'piutang' => $queryPiutang->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $model = new InfoPembayaranPiutang;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $filter_header = []; $count_method = 0;
        if(isset($_GET['advanced-filter'])) {            
            if(isset($_GET['advanced-filter']['tgl_pembayaranpiutang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaranpiutang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaranpiutang']); // Unset Advanced Filter  date range
            }
            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $filter_header["nama_pasien"] = $nama_pasien;
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }
            if(isset($_GET['advanced-filter']['no_rekam_medik'])){
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $filter_header["no_rekam_medik"] = $no_rekam_medik;
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
            }
            if(isset($_GET['advanced-filter']['no_pendaftaran'])){
                $no_pendaftaran = $_GET['advanced-filter']['no_pendaftaran'];
                $filter_header["no_pendaftaran"] = $no_pendaftaran;
                $query->andWhere(['ILIKE', 'no_pendaftaran', $no_pendaftaran]);
            }
            if(isset($_GET['advanced-filter']['no_pembayaranpiutang'])){
                $no_pembayaranpiutang = $_GET['advanced-filter']['no_pembayaranpiutang'];
                $filter_header["no_pembayaranpiutang"] = $no_pembayaranpiutang;
                $query->andWhere(['ILIKE', 'no_pembayaranpiutang', $no_pembayaranpiutang]);
            }
            if(isset($_GET['advanced-filter']['metode_pembayaran'])){
                $metode_pembayaran = $_GET['advanced-filter']['metode_pembayaran'];
                $metode_pembayaran = explode("-", $metode_pembayaran);
                if(count($metode_pembayaran) > 1) {
                    $count_method = 2;
                    $query->andWhere(['metode_pembayaran' => $metode_pembayaran[0]]);
                    $query->andWhere(['jenisnontunai_id' => $metode_pembayaran[1]]);
                } else {
                    $count_method = 1;
                    $query->andWhere(['metode_pembayaran' => $metode_pembayaran[0]]);
                }
            }
        }        
        $query->andWhere(['between', 'tgl_pembayaranpiutang', $start, $end]); 
        $query->orderBy($_GET['order']);

        $result = [];
              
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            
            $newValue[\Yii::t('app', 'Tanggal Pembayaran')] = date('d M Y', strtotime($value['tgl_pembayaranpiutang'])) ;
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'No. RM')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'No. Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No. Pembayaran')] = $value['no_pembayaranpiutang'];
            $metode_pembayaran_label = $count_metode_label = isset($value['metode_pembayaran']) ? $value['metode_pembayaran_nama'] : '-';
            if (isset($value['jenisnontunai_nama'])) {
                $metode_pembayaran_label = $count_metode_labels = $metode_pembayaran_label.' - '.$value['jenisnontunai_nama'];
            }
            $newValue[\Yii::t('app', 'Metode Pembayaran')] = $metode_pembayaran_label;
            $newValue[\Yii::t('app', 'Jumlah Bayar')] = $value['total_bayarpiutang'];
            if (isset($value['total_sisapiutang'])) {
                $newValue[\Yii::t('app', 'Sisa Piutang')] = $value['total_sisapiutang'];
            }
            $result[$key] = $newValue;

            if($count_method == 1) {
                $filter_header["metode_bayar"] = $count_metode_label;
            } else if($count_method == 2) {
                $filter_header["metode_bayar"] = $count_metode_labels;
            }

        }
        // Directory Creation

        $header = array(
            Yii::t('app', "Tanggal Pembayaran") => ((date('d-M-Y', strtotime($start))." - ".date('d-M-Y', strtotime($end)))),
        );
        
        $filter_label = [
            "metode_bayar" => "Metode Pembayaran",
            "no_pembayaranpiutang" => "No. Pembayaran",
            "no_pendaftaran" => "No. Pendaftaran",
            "nama_pasien" => "Nama Pasien",
            "no_rekam_medik" => "No. Rekam Medik"
        ];
        if(!empty($filter_header)) {
            foreach($filter_header as $k => $v) {
                $header[$filter_label[$k]] = $v;
            }
        }
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }
     /**
    * @controller actionCetakPdf
    * @attribute #lapstokbarang# => menampilkan hasil pdf laporan stok barang
    **/
    public function actionCetakPdf()
    {

        $model = new InfoPembayaranPiutang();
        $query = $model::find(true);


        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');        
        if(isset($_GET['advanced-filter'])) {            
            if(isset($_GET['advanced-filter']['tgl_pembayaranpiutang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaranpiutang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pembayaranpiutang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }
            if(isset($_GET['advanced-filter']['no_rekam_medik'])){
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
            }
            if(isset($_GET['advanced-filter']['no_pendaftaran'])){
                $no_pendaftaran = $_GET['advanced-filter']['no_pendaftaran'];
                $query->andWhere(['ILIKE', 'no_pendaftaran', $no_pendaftaran]);
            }
            if(isset($_GET['advanced-filter']['no_pembayaranpiutang'])){
                $no_pembayaranpiutang = $_GET['advanced-filter']['no_pembayaranpiutang'];
                $query->andWhere(['ILIKE', 'no_pembayaranpiutang', $no_pembayaranpiutang]);
            }
        }        
        $query->andWhere(['between', 'tgl_pembayaranpiutang', $start, $end]); 

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = ['data'=>$data];
        $print = new DocoPrint();            
        $print->attributes = [
            '#infpembayaranpiutangpasien#' => $this->renderPartial('index', $result),
        ];
        $print->Output();
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
                SELECT * FROM infopembayaranpiutang_v 
                WHERE pembayaranpiutang_id = {$id}
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
            $print = new DocoPrint('kwt-bayar-piutang');
            $print->attributes = [
              '#no_transaksi#'=>isset($data['no_pembayaranpiutang']) ? $data['no_pembayaranpiutang'] : '',
              '#nama_pasien#' => isset($data['nama_pasien']) ? $data['nama_pasien'] : '',
              '#dari_kepada#' => isset($data['nama_pasien']) ? $data['nama_pasien'] : '',
              '#tgl_pembayaran#'=> $created_date,
              '#tgl_pembayaran2#'=> $created_date2,
              '#jumlah#'=> isset($data['total_bayarpiutang'])? number_format($data['total_bayarpiutang'], 0, ',','.') :'',
              '#nama_pegawai#'=>isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '',
              '#terbilang#' => isset($data['total_bayarpiutang']) ? DocoHelpers::Terbilang($data['total_bayarpiutang']). 'Rupiah' : '',
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
}