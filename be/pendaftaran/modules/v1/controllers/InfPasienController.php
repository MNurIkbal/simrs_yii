<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRjDenganBatalView;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\LapKunjunganRawatDarurat;
use app\modules\v1\models\LapKunjunganRawatInap;
use app\modules\v1\models\InfoKunjunganRiView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\InfoKunjunganRd;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\PenanggungBiaya;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\HistoriPelayanan;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\payload\EditPendaftaranForm;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\PenanggungBiayaForm;
use app\modules\v1\payload\ParamModel;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\models\InfoPasienMcuView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\InfPasienPenunjangDetail;
use app\modules\v1\models\LaporanPenunjangBsl;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\SySyncV;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use app\modules\v1\models\TarifTotalRsFn;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyPasienMasukPenunjangView;
use app\modules\v1\models\SynceditsantoyusupR;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\components\BpjsController;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use yii\helpers\ArrayHelper;
use Doco\models\antrian\AntrianjknV;
use Doco\models\antrian\AntrianjknR;
use app\modules\v1\models\BpjsJkn;
use app\modules\v1\models\LoginJknR;

class InfPasienController extends BpjsController
{

    public $modelClass = 'app\modules\v1\models\inf-pasien';
    protected $_title = 'Laporan Informasi Pasien ';
    protected $komponenTotal;

    const IGD = 'igd';
    const RANAP = 'ranap';
    const RAJAL = 'rajal';
    const MCU = 'mcu';
    const ST_YUSUP = 'st-yusup';
    const PENUNJANG = 'penunjang';
    const PAKET = 'paket';
    const TINDAKAN = 'tindakan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["rajal"] = ["POST", "GET"];
        $verbs["igd"] = ["POST", "GET"];
        $verbs["ranap"] = ["POST", "GET"];
        $verbs["update-create-sep"] = ["PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @param
    * @return json list data
    * @desc
    */
    public function actionRajal()
    {
        return Yii::$app->docoPlugin->execute('kunjungan_rajal');
    }

    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @param
    * @return json list data
    * @desc
    */
    public function actionIgd()
    {
        $model = new LapKunjunganRawatDarurat;
        $query = $model::find();

        // $codeBatal = DocoConstants::STATUS_PERIKSA_BTL_KUNJ;
        // $isHideRegisterCancel = true;
        // if($isHideRegisterCancel){
        //   $query->andWhere(['<>', 'status_periksa_id', $codeBatal]);
        // }

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->getFilterData($query, $advancedFilters);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }



    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @param
    * @return json list data
    * @desc
    */
    public function actionRanap()
    {
        $model = new InfoKunjunganRiView;
        $query = $model::find();

        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $advancedFilters = $request->get('advanced-filter', []);
        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if(isset($advancedFilters['petugas'])) {
                $term = strtolower($advancedFilters['petugas']);
                $query->andWhere(['like', 'LOWER(pembuat_nama)', $term]);
                $query->orWhere(['like', 'LOWER(pembuat_nama)', $term]);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }
        }

        $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
        $query->orderBy(['pasienadmisi_id' => SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @author Sigit Arif Munandar <sigit@docotel.com>
    * @since 2020-08-24 10:02:48
    * @param
    * @return list data penunjang
    * @desc
    */
    public function actionPenunjang()
    {
        $request = Yii::$app->request;
        $model = new InfPasienPenunjang;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->getFilterData($query, $advancedFilters);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel($jenis)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        $data = [];
        if($jenis == 'ranap'){
            $model = new InfoKunjunganRiView;
            $data_select = [
                'tgl_pendaftaran',
                'no_rekam_medik',
                'no_pendaftaran',
                'namadepan',
                'nama_pasien',
                'alamat_pasien',
                'jenis_kelamin',
                'tanggal_lahir',
                'ruangan_nama',
                'jeniskasuspenyakit_nama',
                'kelaspelayanan_nama',
                'kamarruangan_nokamar',
                'nama_pegawai',
                'carabayar_nama',
                'penjamin_nama',
                'status_masuk',
                'status_periksa',
                'is_pasientitipan',
                'is_stoppasientitipan',
                'kelas_ditagihkan_id',
                'kelas_ditagihkan_nama',
                'is_pasientitipan_pk',
                'carabayar_id'
            ];
        }else if($jenis == 'rajal'){
            $model = new LaporanKunjunganRawatJalanView;
            $data_select = [
                'tgl_pendaftaran',
                'no_rekam_medik',
                'no_pendaftaran',
                'namadepan',
                'nama_pasien',
                'alamat_pasien',
                'jenis_kelamin',
                'tanggal_lahir',
                'ruangan_nama',
                'jeniskasuspenyakit_nama',
                'kelaspelayanan_nama',
                'nama_pegawai',
                'carabayar_nama',
                'penjamin_nama',
                'status_masuk',
                'status_periksa'
            ];
        }else if($jenis == 'igd'){
            $model = new LapKunjunganRawatDarurat;
            $data_select = [
                'tgl_pendaftaran',
                'no_rekam_medik',
                'no_pendaftaran',
                'namadepan',
                'nama_pasien',
                'alamat_pasien',
                'jenis_kelamin',
                'tanggal_lahir',
                'ruangan_nama',
                'jeniskasuspenyakit_nama',
                'kelaspelayanan_nama',
                'nama_pegawai',
                'carabayar_nama',
                'penjamin_nama',
                'status_masuk',
                'status_periksa'
            ];
        }else if($jenis == 'mcu') {
            $model = new InfoPasienMcuView;
            $data_select = [
                'tgl_pendaftaran',
                'no_rekam_medik',
                'no_pendaftaran',
                'nama_depan',
                'nama_pasien',
                'alamat_pasien',
                'j_kelamin',
                'tanggal_lahir',
                'ruangan_nama',
                'kelaspelayanan_nama',
                'dokter_penunjang',
                'carabayar_nama',
                'penjamin_nama',
                'status_periksa_nama'
            ];
        } else if ($jenis == 'penunjang') {
            $model = new InfPasienPenunjangDetail;
            $data_select = [
                'tgl_pendaftaran',
                'no_rekam_medik',
                'no_pendaftaran',
                'nama_pasien',
                'alamat_pasien',
                'jeniskelamin',
                'tanggal_lahir',
                'ruangan_nama',
                'kelaspelayanan_nama',
                'nama_pegawai',
                'carabayar_nama',
                'penjamin_nama',
                'nama_status_periksa',
                'tindakan'
            ];
        } else if ($jenis == 'penunjang_bsl') {
            $model = new LaporanPenunjangBsl;
            $data_select = [
                '*'
            ];
        }

        $query = $model::find();
        $query->select($data_select);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:01', strtotime('NOW'));
        $tgl_akhir = date('Y-m-d 23:59:59', strtotime('NOW'));

        $advancedFilters = $request->get('advanced-filter', []);

        if ($jenis == 'penunjang_bsl') {
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                $query->andWhere(['between', 'tgl_pemeriksaan', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['between', 'tgl_pemeriksaan', $tgl_awal, $tgl_akhir]);
            }
        } else {
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            } else {
                $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            }
        }

        $data = $query->asArray()->all();
        if($jenis == 'ranap'){
            $newData = [];
            foreach($data as $k => $v){
                $statusTitipan = '-';
                if($v['carabayar_id'] != 6){
                    if (!empty($data[$k]['is_pasientitipan_pk'])) {
                        if($data[$k]['is_pasientitipan_pk'] == true && $data[$k]['is_stoppasientitipan'] == false){
                            $statusTitipan = $data[$k]['kelas_ditagihkan_nama'];
                        }
                        $v['kelaspelayanan_nama / Kelas_tagihan'] =  $data[$k]['kelaspelayanan_nama'].' / '.$statusTitipan;
                    } else if (empty($data[$k]['is_pasientitipan_pk'])) {
                        if($data[$k]['is_pasientitipan'] == true && $data[$k]['is_stoppasientitipan'] == false){
                            $statusTitipan = $data[$k]['kelas_ditagihkan_nama'];
                        }
                        $v['kelaspelayanan_nama / Kelas_tagihan'] =  $data[$k]['kelaspelayanan_nama'].' / '.$statusTitipan;
                    }
                } else {
                    $v['kelaspelayanan_nama / Kelas_tagihan'] =  $data[$k]['kelaspelayanan_nama'].' / '.$statusTitipan;
                }

                 $newData[$k] = $v;
                 unset($newData[$k]['kelas_ditagihkan_nama']);
                 unset($newData[$k]['kelas_ditagihkan_id']);
                 unset($newData[$k]['is_stoppasientitipan']);
                 unset($newData[$k]['is_pasientitipan']);
                 unset($newData[$k]['is_pasientitipan_pk']);
                 unset($newData[$k]['kelaspelayanan_nama']);
                 unset($newData[$k]['carabayar_id']);
            }
            $data = $newData;
        }

        if ($jenis == 'penunjang_bsl') {
            $this->_title.$jenis = 'Penunjang BSL';

            foreach($data as $key => $value) {
                unset($data[$key]['No.']);
                unset($data[$key]['tgl_pemeriksaan']);
            }
        }

        $header = ['Periode'=> $tgl_awal . ' - '.$tgl_akhir];

        $filePath = DocoHelpers::exportExcel($this->_title.$jenis, $data, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_pasien# => Untuk Menampilkan Tabel pemakaian obat alkes
    * @attribute #periode# => untuk menampilkan nomor pemakaian
    * @attribute #cetak_oleh# => untuk menampilkan Tanggal pemakaian
    * @attribute #tanggal# => untuk menampilkan Ruangan pemakaian
    * @attribute #jenis# => untuk menampilkan Ruangan pemakaian
    * @attribute #kepala# => untuk menampilkan Ruangan pemakaian
    * @attribute #kepalanip# => untuk menampilkan Ruangan pemakaian
    **/

    public function actionExportPdf($jenis,$ruangan)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        $data = [];
        $jenis_title = '';
        if ($jenis == 'ranap') {
            $model = new InfoKunjunganRiView;
            $jenis_title = 'Rawat Inap';
            $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                'namadepan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                'kamarruangan_nokamar','nama_pegawai','carabayar_nama',
                'penjamin_nama','status_masuk','status_periksa', 'is_pasientitipan', 'is_stoppasientitipan', 'kelas_ditagihkan_id', 'kelas_ditagihkan_nama', 'is_pasientitipan_pk'];
        } else if ($jenis == 'rajal') {
            $model = new LaporanKunjunganRawatJalanView;
            $jenis_title = 'Rawat Jalan';
            $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                'namadepan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                'nama_pegawai','carabayar_nama',
                'penjamin_nama','status_masuk','status_periksa'];
        } else if ($jenis == 'igd') {
            $model = new LapKunjunganRawatDarurat;
            $jenis_title = 'Rawat Darurat';
            $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                'namadepan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                'nama_pegawai','carabayar_nama',
                'penjamin_nama','status_masuk','status_periksa'];
        } else if($jenis == 'mcu') {
            $model = new InfoPasienMcuView;
            $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                'nama_depan','nama_pasien','alamat_pasien','j_kelamin', 'tanggal_lahir',
                'ruangan_nama', 'kelaspelayanan_nama',
                'dokter_penunjang','carabayar_nama',
                'penjamin_nama', 'status_periksa_nama'];
        } else if($jenis == 'penunjang') {
            $model = new InfPasienPenunjang;
            $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                'nama_depan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                'ruangan_nama', 'kelaspelayanan_nama',
                'nama_pegawai','carabayar_nama',
                'penjamin_nama', 'nama_status_periksa'];
        }

        $query = $model::find();
        $query->select($data_select);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $request = Yii::$app->request;
        $dataKepala = (PegawaiView::find()->where([
            'ruangan_id' => $ruangan,
            'jabatan_id' => DocoConstants::VAR_J_K_R])->one())
                ? PegawaiView::find()->where([
                    'ruangan_id' => $ruangan,
                    'jabatan_id' => DocoConstants::VAR_J_K_R])->one()
                : '';
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        $data = $query->asArray()->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#table_pasien#' => $this->renderPartial('index',[
                'detail' => $data,
                'jenis' => $jenis
            ]),
            '#periode#' => $tgl_awal.'-'.$tgl_akhir,
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#tanggal#' => date('d F Y'),
            '#jenis#'=>strtoupper($jenis_title),
            '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
            '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
        ];

        $print->Output();
    }

    public function actionUpdatePendaftaran()
    {
        return Yii::$app->docoPlugin->execute('update_pendaftaran');
    }

    protected function buildPayload($value, $model)
    {
        return [
            'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
            'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
            'dokter_id' => !empty($model->pegawai_id) ? $model->pegawai_id : $value['dokterpenanggungjawab_id'],
            'perawat_id' => $value['perawat1_id'],
            'perawat2_id' => $value['perawat2_id'],
            'tipepaket_id' => $value['tipepaket_id'],
            'daftartindakan_id' => $value['daftartindakan_id'],
            'qty' => $value['qty_tindakan'],
            'is_cyto' => $value['cyto_tindakan'],
            'is_penyulit' => $value['penyulit_tindakan'],
            'implementasi_id' => $value['implementasi_id'],
            'instruksitindakan_id' => $value['instruksitindakan_id'],
            'instalasi_id' => $value['instalasi_id'],
            'ruangan_id' => $value['ruangan_id'],
            'kelaspelayanan_id' => $value['kelaspelayanan_id'],
            'is_penatajasa' => $value['is_penatajasa'],
            'penjamin_id' => $model->penjamin_id
        ];
    }

    protected function parsingKomponen(array $listKomponen, $isCyto, $persenCyto, $qty)
    {
        $parsingKomponen = [];
        foreach ($listKomponen as $value) {
            $row = $value;
            $hargaSatuan = isset($row['tarif_kompsatuan']) ? $row['tarif_kompsatuan'] : 0;
            $totalHarga = $hargaSatuan * $qty;
            $hargaCyto = 0;
            if ($isCyto) {
                $hargaCyto = ($persenCyto / 100) * $hargaSatuan;
                $totalHarga = ($hargaSatuan + $hargaCyto)  * $qty;
            }
            $row['tarif_tindakankomp'] = $totalHarga;
            $row['tarifcyto_tindakankomp'] = $hargaCyto;
            $parsingKomponen[] = $row;
        }
        return ['list_komponen' => $parsingKomponen];
    }

    protected function listKomponen(array $data)
    {
        $tmpListKomponen = [
            self::TINDAKAN => [],
            self::PAKET => []
        ];

        foreach ($data as $value) {
            $tindakanPaket = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            $komponenTarif = isset($value['komponentarif_id']) ? $value['komponentarif_id'] : null;
            $harga = isset($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] : 0;
            $persenCyto = isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : 0;
            $tipePaket = isset($value['tipepaket_id']) ? $value['tipepaket_id'] : 0;
            $keyHist = self::TINDAKAN;

            if (!empty($tipePaket)) {
                $tindakanPaket = isset($value['tipepaket_id']) ? $value['tipepaket_id'] : null;
                $keyHist = self::PAKET;
            }

            if ($komponenTarif !== $this->komponenTotal) {
                $tmpListKomponen[$keyHist][$tindakanPaket][] = [
                    'komponentarif_id' => $komponenTarif,
                    'tindakanpelayanan_id' => null,
                    'tarif_kompsatuan' => (float) $harga,
                    'tarif_tindakankomp' => 0,
                    'tarifcyto_tindakankomp' => 0,
                    'subsidiasuransikomp' => null,
                    'subsidipemerintahkomp' => null,
                    'subsidirumahsakitkomp' => null,
                    'iurbiayakomp' => null,
                ];
            }
        }
        return $tmpListKomponen;
    }

    private function deleteSep(array $dataBpjs)
    {
        $model = new Bpjs;
        $model->t_sep = $dataBpjs;
        $result = $model->deleteSep();
        return $result;
    }

    private function deleteDataAsuransi($asuransipasien_id, $pendaftaran_id)
    {
        // hapus asuransipasien_id di pendaftaran
        $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        if($pendaftaran) {
            $pendaftaran->asuransipasien_id = null;
            $pendaftaran->save();
        }

        // hapus pendaftaran_id di asuransipasien_m
        $asuransipasien = AsuransiPasien::findOne($asuransipasien_id);
        if($asuransipasien) {
            $asuransipasien->pendaftaran_id = null;
            $asuransipasien->save();
        }

        return true;
    }

    private function deleteDataPenanggungBiaya($penanggungbiaya_id, $pendaftaran_id)
    {
        // hapus penanggungbiaya_id di pendaftaran
        $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        if($pendaftaran) {
            $pendaftaran->penanggungbiaya_id = null;
            $pendaftaran->save();
        }

        return true;
    }

    /**
    * @author Budi
    * @since 2019-01-14 16:14:00
    * @param
    * @return json list data
    * @desc
    */
    public function actionMcu()
    {
        $model = new InfoPasienMcuView;
        $query = $model::find();
        $query->select([
            'infopasienmcu_v.*',
            'infopasienmcu_v.dokter_penunjang as nama_pegawai',
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->getFilterData($query, $advancedFilters);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo Fungsi untuk stop pasien titipan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionStopPasienTitipan($pendaftaran_id)
    {
        $titipan = true;
        $sudahStop = false;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $payload = new ParamModel;
            $payload->pendaftaran_id = $pendaftaran_id;

            if ($payload->validate()) {
                $pindahKamar = PindahKamar::find()
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->orderBy(['pindahkamar_id' => SORT_DESC])
                ->one();

                if ($pindahKamar) {
                    if ($pindahKamar->is_pasientitipan == true) {
                        if ($pindahKamar->is_stoptitipan == false) {
                            $pindahKamar->is_stoptitipan = true;
                            $pindahKamar->save(false);
                            $transaction->commit();

                            return DocoHelpers::callBack(
                                DocoMessages::KEY_UPDATED, [
                                    'text' => 'Kelas Titipan Berhasil Dihentikan.'
                                ]
                            );
                        } else {
                            $sudahStop = true;
                        }
                    }
                } else {
                    $pasienAdmisi = PasienAdmisi::find()
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->one();

                    if ($pasienAdmisi) {
                        if ($pasienAdmisi->is_pasientitipan == true) {
                            if ($pasienAdmisi->is_stoptitipan == false) {
                                $pasienAdmisi->is_stoptitipan = true;
                                $pasienAdmisi->save(false);
                                $transaction->commit();

                                return DocoHelpers::callBack(
                                    DocoMessages::KEY_UPDATED, [
                                        'text' => 'Kelas Titipan Berhasil Dihentikan.'
                                    ]
                                );
                            } else {
                                $sudahStop = true;
                            }
                        }
                    }
                }

                if ($sudahStop == true) {
                    return DocoHelpers::callBack(
                        DocoMessages::KEY_ERR_VALIDATION, [
                            'text' => Yii::t('app', 'Kelas titipan pasien sudah di stop.')
                        ]
                    );
                }

                return DocoHelpers::callBack(
                    DocoMessages::KEY_ERR_VALIDATION, [
                        'text' => Yii::t('app', 'Pasien tidak menggunakan kelas titipan.')
                    ]
                );
            } else {
                return DocoHelpers::callBack(
                    DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $payload->errors
                    ]
                );
            }
        } catch (Exception $e) {
            $transaction->rollBack();

            return DocoHelpers::callBack(
                DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => DocoMessages::ERR_MESSAGE
                ]
            );
        }
    }

    public function actionGetDataSync()
    {
        $model = SyncsantoyusupR::find()
        ->where(['is_sync' => false])
        ->count();

        return $model;
    }

     /**
     * This function will get data pendaftaran
     * and send it to serconn for
     * Sync Santo Yusup
     *
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionSyncPendaftaran()
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = $penunjang = $idArray = [];
        $post = Yii::$app->request->post();
        $cache = Yii::$app->cache;
        $status = $cache->get('sync-pendaftaran');
        $idPendaftaran = isset($post['pendaftaran_id']) ? $post['pendaftaran_id'] : null;
        if(isset($post['id'])) {
            $idArray = $post['id'];
        }
        $route = '';

        if(empty($status)) {
            $status = $cache->set('sync-pendaftaran', 1);
            $model = SyncsantoyusupR::find()
                ->select(['pendaftaran_id'])
                ->where(['is_sync' => false]);
                if(!is_null($idPendaftaran)) {
                    $model->andWhere(['pendaftaran_id' => $idPendaftaran]);
                } else if (!empty($idArray)) {
                    $model->andWhere([
                        'IN', 'syncsantoyusup_id', $idArray,
                    ]);
                }

            $data = $model->all();

            foreach($data as $key => $value) {
                $pdftrn = SyPendaftaranView::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->asArray()
                ->one();

                $add = json_decode($pdftrn['additional_data'], true);

                if(!empty($pdftrn['asuransipasien_id'])) {
                    $asuransi = SyAsuransiPasienView::find()
                    ->where(['asuransipasien_id' => $pdftrn['asuransipasien_id']])
                    ->asArray()
                    ->one();
                }

                //** keluarga harus selalu di kirim */
                // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                    $keluarga = SyKeluargaPasienView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['keluargapasien_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                // }

                if(isset($add['penanggungbiaya']) && !empty($add['penanggungbiaya'])) {
                    $penanggung = SyPenanggungBiayaView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['penanggungbiaya_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                }

                if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                    $penanggungJawab = SyPenanggungJawabView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['penanggungjawab_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                }

                $pasien = SyPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->asArray()
                ->one();

                if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                    $umurExp = explode(" ",$pdftrn['umur']);
                    $pdftrn['umur_hari'] = $umurExp[4];
                    $pdftrn['umur_bulan'] = $umurExp[2];
                    $pdftrn['umur_tahun'] = $umurExp[0];
                }

                if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                    $additionalPasien = json_decode($pasien['additional_pasien']);
                    if (!empty($additionalPasien)) {
                        foreach ($additionalPasien as $key => $val) {
                            if (isset($val->jenisidentitas) && $val->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                                $ktp = $val->no_identitas_pasien;
                            }
                        }
                        if(isset($ktp)) {
                            $pasien['nik'] = $ktp;
                        }
                    }
                }

                if (isset($pdftrn['dokterkonsul']) && !empty($pdftrn['dokterkonsul'])) {
                    $listDokter = json_decode($pdftrn['dokterkonsul']);
                    $pdftrn['dok_konsul'] = [];
                    $getDokter = Pegawai::find()
                                ->select('additional_data')
                                ->where(['IN', 'pegawai_id', $listDokter])
                                ->asArray()
                                ->all();
                    for ($i=0; $i < count($getDokter); $i++) {
                        $pdftrn['dok_konsul'][] = $getDokter[$i]['additional_data'];
                    }
                }

                $penjamin = SyPenjaminView::find()
                ->where(['penjamin_id' => $pdftrn['penjamin_id']])
                ->asArray()
                ->one();

                $penunjang = SyPasienMasukPenunjangView::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->asArray()
                ->one();

                $result = [
                    'pendaftaran' => $pdftrn,
                    'pasien' => $pasien,
                    'asuransi' => $asuransi,
                    'keluarga' => $keluarga,
                    'penanggung' => $penanggung,
                    'penjamin' => $penjamin,
                    'penanggungJawab' => $penanggungJawab,
                    'penunjang' => $penunjang,
                ];

                $route = $pdftrn['instalasi_id'];

                switch ($route) {
                    case DocoConstants::VAR_I_RJ:
                        $route = 'app/save-pendaftaran-rajal';
                        break;
                    case DocoConstants::VAR_I_RD:
                        $route = 'app/save-pendaftaran-igd';
                        break;
                    case DocoConstants::VAR_I_RANAP:
                        $route = 'app/save-pendaftaran-ranap';
                        break;
                    default:
                        $route = 'app/save-pendaftaran-penunjang';
                        break;
                }

                $params['route'] = $route;
                $params['data'] = $result;
                (new PendaftaranService)->syncPendaftaranSty($params);

                // return true;
            }
            $cache->delete('sync-pendaftaran');
        }
        // $cache->delete('sync-pendaftaran');
    }

    public function actionResyncTransaction()
    {
        $request = Yii::$app->request;
        $type = $request->get('model');
        switch ($type) {
            case 'registrasi':
                return $this->actionSyncPendaftaran();
                break;
            case 'edit_regis':
                return $this->actionSyncEditPendaftaran();
                break;
            default:
                return null;
                break;
        }
    }

    public function actionSyncEditPendaftaran()
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = $penunjang = $idArray = [];
        $post = Yii::$app->request->post();
        $cache = Yii::$app->cache;
        $type = (!is_null($post['type'])) ? $post['type'] : "";
        $status = $cache->get('sync-pendaftaran-'.$type);
        $idPendaftaran = isset($post['pendaftaran_id']) ? $post['pendaftaran_id'] : null;
        if(isset($post['id'])) {
            $idArray = $post['id'];
        }
        $route = '';

        if(empty($status)) {
            $status = $cache->set('sync-pendaftaran-'.$type, 1);
            $model = SynceditsantoyusupR::find()
                ->select(['pendaftaran_id'])
                ->where([
                    'state' => $type,
                    'is_sync' => false
                ]);
                if(!is_null($idPendaftaran)) {
                    $model->andWhere(['pendaftaran_id' => $idPendaftaran]);
                } else if (!empty($idArray)) {
                    $model->andWhere([
                        'IN', 'synceditsantoyusup_id', $idArray,
                    ]);
                }

            $data = $model->all();

            foreach($data as $key => $value) {
                $pdftrn = SyPendaftaranView::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->asArray()
                ->one();

                $add = json_decode($pdftrn['additional_data'], true);

                if(!empty($pdftrn['asuransipasien_id'])) {
                    $asuransi = SyAsuransiPasienView::find()
                    ->where(['asuransipasien_id' => $pdftrn['asuransipasien_id']])
                    ->asArray()
                    ->one();
                }

                //** keluarga harus selalu di kirim */
                // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                    $keluarga = SyKeluargaPasienView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['keluargapasien_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                // }

                if(isset($add['penanggungbiaya']) && !empty($add['penanggungbiaya'])) {
                    $penanggung = SyPenanggungBiayaView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['penanggungbiaya_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                }

                if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                    $penanggungJawab = SyPenanggungJawabView::find()
                    ->where(['pasien_id' => $pdftrn['pasien_id']])
                    ->orderBy(['penanggungjawab_id' => SORT_DESC])
                    ->asArray()
                    ->one();
                }

                $pasien = SyPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->asArray()
                ->one();

                if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                    $umurExp = explode(" ",$pdftrn['umur']);
                    $pdftrn['umur_hari'] = $umurExp[4];
                    $pdftrn['umur_bulan'] = $umurExp[2];
                    $pdftrn['umur_tahun'] = $umurExp[0];
                }

                if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                    $additionalPasien = json_decode($pasien['additional_pasien']);
                    if (!empty($additionalPasien)) {
                        foreach ($additionalPasien as $key => $val) {
                            if (isset($val->jenisidentitas) && $val->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                                $ktp = $val->no_identitas_pasien;
                            }
                        }
                        if(isset($ktp)) {
                            $pasien['nik'] = $ktp;
                        }
                    }
                }

                if (isset($pdftrn['dokterkonsul']) && !empty($pdftrn['dokterkonsul'])) {
                    $listDokter = json_decode($pdftrn['dokterkonsul']);
                    $pdftrn['dok_konsul'] = [];
                    $getDokter = Pegawai::find()
                                ->select('additional_data')
                                ->where(['IN', 'pegawai_id', $listDokter])
                                ->asArray()
                                ->all();
                    for ($i=0; $i < count($getDokter); $i++) {
                        $pdftrn['dok_konsul'][] = $getDokter[$i]['additional_data'];
                    }
                }

                $penjamin = SyPenjaminView::find()
                ->where(['penjamin_id' => $pdftrn['penjamin_id']])
                ->asArray()
                ->one();

                $penunjang = SyPasienMasukPenunjangView::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->asArray()
                ->one();

                $result = [
                    'pendaftaran' => $pdftrn,
                    'pasien' => $pasien,
                    'asuransi' => $asuransi,
                    'keluarga' => $keluarga,
                    'penanggung' => $penanggung,
                    'penjamin' => $penjamin,
                    'penanggungJawab' => $penanggungJawab,
                    'penunjang' => $penunjang,
                ];

                $route = $pdftrn['instalasi_id'];
                if($type == 'edit') {
                    switch($route){
                        case DocoConstants::VAR_I_RJ:
                            $route = 'app/update-pendaftaran-rajal';
                            break;
                        case DocoConstants::VAR_I_RD:
                            $route = 'app/update-pendaftaran-igd';
                            break;
                        case DocoConstants::VAR_I_RANAP:
                            $route = 'app/update-pendaftaran-ranap';
                            break;
                        default:
                            $route = 'app/update-pendaftaran-penunjang';
                            break;
                    }

                    $params['route'] = $route;
                    $params['data'] = $result;
                    (new PendaftaranService)->syncPendaftaranSty($params);
                } else if ($type == 'batal') {
                    switch($route){
                        case DocoConstants::VAR_I_RD:
                            $route = 'app/batal-pendaftaran-igd';
                            break;
                        case DocoConstants::VAR_I_RJ:
                            $route = 'app/batal-pendaftaran-rajal';
                            break;
                        case DocoConstants::VAR_I_RANAP:
                            $route = 'app/update-pendaftaran-ranap';
                            break;
                        default:
                            $route = 'app/batal-pendaftaran-penunjang';
                            break;
                    }

                    $params['route'] = $route;
                    $params['data'] = $this->syncUpdatePendaftaran($model->pendaftaran_id);
                    (new PendaftaranService)->syncPendaftaranSty($params);
                }
            }
            $cache->delete('sync-pendaftaran-'.$type);
        }
    }

    public function actionSyncFile()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $filter = isset($getData['params']) ? $getData['params'] : [];
        $jenis = isset($filter['jenis']) ? $filter['jenis'] : 'rajal';
        $tipe = isset($getData['tipe']) ? $getData['tipe'] : 'pdf';
        $isExcel = ($tipe == 'excel') ? true : false;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if (isset($filter['page'])) unset($filter['page']);
        if (isset($filter['per-page'])) unset($filter['per-page']);
        $fetchLimit = 20;
        if($isExcel) {
            $countData = count($this->actionGetObjectData());
        }
        else {
            $data = $this->actionGetObjectData();
            $countData = isset($data['countData']) ? $data['countData'] : 0;
        }
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [
                'InformasiPendaftaran' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'isExcel' => $isExcel,
                    'jenis' => $jenis
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'CetakInformasiPendaftaran' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'isExcel' => $isExcel,
                    'jenis' => $jenis,
                    'getData' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadInformasiPendaftaran' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'isExcel' => $isExcel,
                    'jenis' => $jenis
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
            'isExcel' => $isExcel,
            'jenis' => $jenis
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $tipe = $request->get('tipe', null);
        $ext = ($tipe == 'excel') ? '.xlsx' : '.pdf';
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.$ext;
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            if($tipe == 'excel') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
            else {
                header('Content-Type: application/pdf');
            }
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    public function checkUsingExtension() {
        $cekExtension = Yii::$app->docoPlugin->checkExtension('kunjungan_rajal');

        return $cekExtension;
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $is_executive = $request->get('is_executive', false);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $params = isset($getData['params']) ? $getData['params'] : [];
        $advancedFilters = [];
        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData['advanced-filter'];
        }

        if(isset($params['advanced-filter'])) {
            $advancedFilters = $params['advanced-filter'];
        }
        $jenis_title = '';
        $jenis = isset($params['jenis']) ? $params['jenis'] : 'rajal';
        $ruangan = isset($params['ruangan']) ? $params['ruangan'] : '';
        $isExcel = isset($getData['tipe']) ? $getData['tipe'] : '';
        $isExcel = (!empty($isExcel && $isExcel == 'excel')) ? true : false;
        $filterDate = 'tgl_pendaftaran';
        $orderBy = 'pendaftaran_id';
        $data_select = [];

        $checkExtension = $this->checkUsingExtension();

        switch ($jenis) {
            case 'ranap':
                $model = new InfoKunjunganRiView;
                $filterDate = 'tgl_admisi';
                $orderBy = 'pasienadmisi_id';
                $jenis_title = 'Rawat Inap';
                break;

            case 'rajal':
                if($checkExtension) {
                    $model = new LaporanKunjunganRjDenganBatalView;
                } else {
                    $model = new LaporanKunjunganRawatJalanView;
                }
                $jenis_title = 'Rawat Jalan';
                break;

            case 'igd':
                $model = new LapKunjunganRawatDarurat;
                $jenis_title = 'Rawat Darurat';
                break;

            case 'mcu':
                $model = new InfoPasienMcuView;
                $jenis_title = 'MCU';
                break;

            default:
                $model = new InfPasienPenunjang;
                $jenis_title = 'Penunjang';
                break;
        }

        if(!$isExcel) {
            if($jenis == 'ranap') {
                $data_select = ['tgl_pendaftaran', 'no_pendaftaran', 'no_rekam_medik',
                    'nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                    'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                    'kamarruangan_nokamar','nama_pegawai','carabayar_nama', 'namadepan',
                    'penjamin_nama','status_periksa', 'is_pasientitipan', 'is_stoppasientitipan',
                    'kelas_ditagihkan_id', 'kelas_ditagihkan_nama', 'is_pasientitipan_pk'];
            }
            else if ($jenis == 'rajal') {
                $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                    'namadepan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                    'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                    'nama_pegawai','carabayar_nama',
                    'penjamin_nama','status_periksa'];
            } else if ($jenis == 'igd') {
                $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                    'namadepan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                    'ruangan_nama','jeniskasuspenyakit_nama','kelaspelayanan_nama',
                    'nama_pegawai','carabayar_nama',
                    'penjamin_nama','status_periksa'];
            } else if($jenis == 'mcu') {
                $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                    'nama_depan','nama_pasien','alamat_pasien','j_kelamin', 'tanggal_lahir',
                    'ruangan_nama', 'kelaspelayanan_nama',
                    'dokter_penunjang','carabayar_nama',
                    'penjamin_nama', 'status_periksa_nama'];
            } else if($jenis == 'penunjang') {
                $data_select = ['tgl_pendaftaran', 'no_rekam_medik','no_pendaftaran',
                    'nama_depan','nama_pasien','alamat_pasien','jenis_kelamin', 'tanggal_lahir',
                    'ruangan_nama', 'kelaspelayanan_nama',
                    'nama_pegawai','carabayar_nama',
                    'penjamin_nama', 'nama_status_periksa'];
            }
        }

        $query = $model::find();
        if($isExcel && $jenis == 'mcu') {
            $query->select([
                'infopasienmcu_v.*',
                'infopasienmcu_v.dokter_penunjang as nama_pegawai',
            ]);
        }

        if(!empty($data_select)) {
            $query->select($data_select);
        }

        if ($jenis == 'rajal') {
            if ($is_executive) {
                $query->andWhere(['is_executive' => true]);
            }
        }

        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $start = $advancedFilters['tgl_pendaftaran_awal'];
                $end = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if(isset($advancedFilters['petugas'])) {
                $term = strtolower($advancedFilters['petugas']);
                $query->andWhere(['like', 'LOWER(pembuat_nama)', $term]);
                $query->orWhere(['like', 'LOWER(pembuat_nama)', $term]);
            }

            if(isset($advancedFilters['no_pendaftaran'])) {
                $term = $advancedFilters['no_pendaftaran'];
                $query->andWhere(['no_pendaftaran' => $term]);
            }

            if(isset($advancedFilters['nosep'])) {
                $term = $advancedFilters['nosep'];
                $query->andWhere(['nosep' => $term]);
            }

            if(isset($advancedFilters['nama_pasien'])) {
                $term = strtolower($advancedFilters['nama_pasien']);
                $query->andWhere(['LIKE', 'LOWER(nama_pasien)', $term]);
            }

            if(isset($advancedFilters['no_rekam_medik'])) {
                $term = $advancedFilters['no_rekam_medik'];
                $query->andWhere(['no_rekam_medik' => $term]);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($advancedFilters['status_periksa_id'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa_id']]);
                unset($_GET['advanced-filter']['status_periksa_id']);
            }

            if(isset($advancedFilters['carabayar_id'])) {
                $query->andWhere(['carabayar_id' => $advancedFilters['carabayar_id']]);
            }

            if(isset($advancedFilters['penjamin_id'])) {
                $query->andWhere(['penjamin_id' => $advancedFilters['penjamin_id']]);
            }

            if(isset($advancedFilters['pegawai_id'])) {
                $query->andWhere(['pegawai_id' => $advancedFilters['pegawai_id']]);
            }

            if(isset($advancedFilters['ruangan_id']) && is_array(explode(',', $advancedFilters['ruangan_id'])) ){
                $query->andWhere(['in', 'ruangan_id', explode(',', $advancedFilters['ruangan_id'])]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }
        }

        $query->andWhere(['between', $filterDate, $start, $end]);
        $query->orderBy([$orderBy => SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $model = $query->asArray()->all();
        $result = $model;

        if(!$isExcel) {
            $periode = date('d-M-Y H:i:s', strtotime($start)).' - '.date('d-M-Y H:i:s', strtotime($end));
            $dataKepala = (PegawaiView::find()->where([
                'ruangan_id' => $ruangan,
                'jabatan_id' => DocoConstants::VAR_J_K_R])->one())
                    ? PegawaiView::find()->where([
                        'ruangan_id' => $ruangan,
                        'jabatan_id' => DocoConstants::VAR_J_K_R])->one()
                    : '';

            $attributes = [
                '#table_pasien#' => $this->renderPartial('index',[
                    'detail' => $model,
                    'jenis' => $jenis
                ]),
                '#periode#' => $periode,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#tanggal#' => date('d F Y'),
                '#jenis#' => strtoupper($jenis_title),
                '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
                '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
            ];
            $result = [
                'periode' => $periode,
                'attributes' => $attributes,
                'countData' => count($model),
            ];
        }

        return $result;
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

    private function getFilterData($query, $advancedFilters)
    {
        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if(isset($advancedFilters['petugas'])) {
                $term = strtolower($advancedFilters['petugas']);
                $query->andWhere(['like', 'LOWER(pembuat_nama)', $term]);
                $query->orWhere(['like', 'LOWER(pembuat_nama)', $term]);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);

        return $query;
    }

    public function actionGetBundleBpjs()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $jenis = $request->get('jenis',null);

        if(!is_null($jenis) && $jenis =='ranap'){
            $query = "
            SELECT bt.* FROM pasienadmisi_t pt
            LEFT JOIN bpjs_t bt ON bt.bpjs_id = pt.bpjs_id
            WHERE pt.pendaftaran_id = :pendaftaran_id
            ";
            $data_bpjs = Yii::$app->db->createCommand($query)->bindParam(":pendaftaran_id",$pendaftaran_id)->queryOne();
        } else{

            $data_bpjs = Bpjs::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->one();
        }

        $data_pasien = InfoKunjunganRsView::find()
        ->select([
            'pasien_id',
            'no_rekam_medik',
            'nama_pasien',
            'no_telepon_pasien',
            'no_identitas_pasien'
        ])->where(['pendaftaran_id' => $pendaftaran_id])
        ->one();

        return [
            'data_bpjs' => $data_bpjs,
            'data_pasien' => $data_pasien
        ];
    }

    public function actionGetDataRiwayatPerubahanDataPendaftaran($id)
    {
        $model = new \SirsCore\models\LogActivityR;
	    $query = $model::find()
            ->select([
                'logactivity_r.additional_detail',
                'logactivity_r.tgl',
                'pegawai_m.nama_pegawai as nama_pegawai'
            ])
            ->leftJoin('loginpemakai_k','loginpemakai_k.loginpemakai_id = logactivity_r.created_by')
            ->leftJoin('pegawai_m','pegawai_m.pegawai_id = loginpemakai_k.pegawai_id')
            ->where([
                'tipe' => ['PENDAFTARAN_RAJAL','PENDAFTARAN_RANAP'],
                'aksi' => 'edit',
                'transaksi_id' => $id
            ]);
	    $query->orderBy(['logactivity_r.created_date' => SORT_DESC]);

        return new ArrayDataProvider([
            'allModels' => $query->asArray()->all(),
            'sort' => [
                'attributes' => ['logactivity_r.created_date'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }

    /**
     * param: int pendaftaran_id
     * model: BpjsNewForm
     */
    public function actionUpdateCreateSep($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $isRanap = false;
        $isPasienBaru = false;
        $simpanAntrian = []; //default response simpan antrian jkn
        $antrianJkn = []; //default antrianjkn jkn

        $model = new BpjsNewForm;
        $model->attributes = $post;

        if (ArrayHelper::getValue($post, 'is_naikkelas_ranap', null) == true) {
            $model->klsRawatNaik = ArrayHelper::getValue($post, 'naik_kelas_rawat_inap');
            $model->pembiayaan = ArrayHelper::getValue($post, 'pembiayaan');
            $model->penanggungJawab = ArrayHelper::getValue($post, 'nama_penganggung_jawab');
        }

        if ($model->jenis_pelayanan == DocoConstants::PELAYANAN_BPJS_RANAP) $isRanap = true;

        if ($model->validate()) {

            if((new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1){
            
                $antrianJkn = AntrianjknV::find()
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])->asArray()->one();

                $getDataAdditional = $this->_getInfoPendaftaran($pendaftaran_id);

                $tanggalperiksa = date('Y-m-d', strtotime(ArrayHelper::getValue($antrianJkn,'tanggal_periksa')));
                $jamMulai = ArrayHelper::getValue($getDataAdditional,'jammulai');

                $totalAntrian = ArrayHelper::getValue($getDataAdditional,'kuotajkn', 0) + ArrayHelper::getValue($getDataAdditional,'kuotanonjkn', 0);
                $sisaAntrian = ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn', 0) + ArrayHelper::getValue($getDataAdditional,'sisakuotajkn',0);
                $spm = ArrayHelper::getValue($getDataAdditional,'estimasidilayani', 15);
                $tglDilayani = $tanggalperiksa . ' ' . $jamMulai;
                $noUrut = ($totalAntrian - $sisaAntrian);
        
                $timestampsecond = (date_create($tglDilayani)->getTimestamp()) + (
                    (($spm * $noUrut) * 60)
                );
        
                $estimasiDilayani = $timestampsecond * 1000;

                $lastSep = $this->getLastSepByRujukan(ArrayHelper::getValue($post, 'info_response.lastSep.response.histori'), $model->no_rujukan);
                $post_ranap = ArrayHelper::getValue($post, 'info_response.post_ranap', false);
                // $jenisKunjunganJkn = !empty($model->no_surat_kontrol) ? 3 : ArrayHelper::getValue($antrianJkn,'jeniskunjungan');
                if(empty($lastSep) && $model->asal_rujukan == 1){ // 1
                    $jenisKunjunganJkn = DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_FKTP_JKN];
                    $nomorReferensiJkn = $model->no_rujukan;
                } else if ((empty($lastSep) && $model->asal_rujukan == 2) && empty($post_ranap)){ // 4
                    $jenisKunjunganJkn = DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_RS_JKN];
                    $nomorReferensiJkn = $model->no_rujukan;
                } else if ((!empty($lastSep) && $model->poli_tujuan == ArrayHelper::getValue($lastSep, 'poliTujSep')) || !empty($post_ranap)){ // 3
                    $jenisKunjunganJkn = DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_KONTROL];
                    $nomorReferensiJkn = $model->no_surat_kontrol;
                } else if (!empty($lastSep) && $model->poli_tujuan != ArrayHelper::getValue($lastSep, 'poliTujSep')){ // 2
                    $jenisKunjunganJkn = DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_INTERNAL];
                    $nomorReferensiJkn = $model->no_surat_kontrol;
                }

                $infoBpjs = [];

                if (!empty($model->info_response) && is_array($model->info_response)) {
                    $infoBpjs = $model->info_response;
                }
                $nik = isset($infoBpjs['peserta']['nik']) ? $infoBpjs['peserta']['nik'] : '-';

                $request = [
                    'kodebooking' => ArrayHelper::getValue($antrianJkn,'kodebooking'),//
                    'jenispasien' => 'JKN',// hard code to jkn karena ketika create sep otomatis antrian jkn
                    'nomorkartu' => ArrayHelper::getValue($post,'no_kartu'),//
                    'nik' => $nik ? $nik : '',
                    'nohp' => ArrayHelper::getValue($post,'no_telp'),//
                    'kodepoli' => ArrayHelper::getValue($getDataAdditional,'kodepoli'),//
                    'namapoli' => ArrayHelper::getValue($getDataAdditional,'namapoli'),//
                    'pasienbaru' => 0,
                    'norm' => ArrayHelper::getValue($antrianJkn,'no_rekam_medik'),//
                    'tanggalperiksa' => $tanggalperiksa,//
                    'kodedokter' => ArrayHelper::getValue($getDataAdditional,'kodedokter'),//
                    'namadokter' => ArrayHelper::getValue($getDataAdditional,'namadokter'),//
                    'jampraktek' => ArrayHelper::getValue($getDataAdditional,'jampraktek'),//
                    'jeniskunjungan' => $jenisKunjunganJkn,//
                    'nomorreferensi' => $nomorReferensiJkn,//
                    'nomorantrean' => ArrayHelper::getValue($antrianJkn,'nomorantrean'),//
                    'angkaantrean' => ArrayHelper::getValue($antrianJkn,'angkaantrean'),//
                    'estimasidilayani' => $estimasiDilayani,//
                    'sisakuotajkn' => ArrayHelper::getValue($getDataAdditional,'sisakuotajkn'),//
                    'kuotajkn' => ArrayHelper::getValue($getDataAdditional,'kuotajkn'),//
                    'sisakuotanonjkn' => ArrayHelper::getValue($getDataAdditional,'sisakuotanonjkn'),//
                    'kuotanonjkn' => ArrayHelper::getValue($getDataAdditional,'kuotanonjkn'),//
                    'keterangan' => ArrayHelper::getValue($antrianJkn,'keterangan'),//
                ];

                $simpanAntrian = (new BpjsJkn)->simpanAntrianJkn($request);
 
                $this->setLogs($simpanAntrian, $request, $pendaftaran_id);

            }

            try{
                $bpjsRes = $this->saveBpjs($model, $pendaftaran_id, $isPasienBaru, $isRanap, true);
                if (is_array($bpjsRes)) {
                    // $this->rollbackAntrianJkn($antrianJkn, $simpanAntrian); // handle sementara untuk 422 // sementara comment
                    return $bpjsRes;
                }

                // ketika create sep berhasil, maka simpan data antrian offline ke antrianjknr
                if ((new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1) {
                    $antrianjkn_r = AntrianjknR::find()->where(['pendaftaran_id' => $pendaftaran_id])->one(); // get antrianjkn offline
                    if ($antrianjkn_r) {
                        $additional_jkn = [
                            'response' => $simpanAntrian,
                            'payload' => $request
                        ];
                        $additional_jkn = json_encode($additional_jkn);

                        $antrianjkn_r->nomorkartu = $request['nomorkartu'];
                        $antrianjkn_r->nomorreferensi = $nomorReferensiJkn;
                        $antrianjkn_r->jeniskunjungan = $jenisKunjunganJkn;
                        $antrianjkn_r->additional_jkn = $additional_jkn;

                        $antrianjkn_r->save();
                    }
                }
                
                $bpjs = Bpjs::find()
                ->select(['nosep'])
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();
                    return [
                        'title' => 'Proses Berhasil',
                        'text' => 'No SEP telah berhasil diterbitkan',
                        'no_sep' => $bpjs['nosep'],
                    ];
            }catch (\yii\db\Exception $e){
                Yii::error($e);

                // $this->rollbackAntrianJkn($antrianJkn, $simpanAntrian); sementara comment

                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => $e->getMessage(),
                    'flag' => 'model_error'
                ];
            }
            catch (\Exception $e){
                Yii::error($e);

                // $this->rollbackAntrianJkn($antrianJkn, $simpanAntrian); sementara comment

                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => $e->getMessage(),
                    'flag' => 'model_error'
                ];
            }


            
        } else {
            Yii::error($model);
            return [
                'status' => 422,
                'title' => 'Proses BPJS Gagal!',
                'text' => 'Silahkan cek inputan',
                'data' => $model->errors,
                'flag' => 'model_error'
            ];
        }
    }

    protected function setLogs($response, $data, $idPendaftaran = null)
    {
        $model = new LoginJknR;
        $model->pendaftaran_id = $idPendaftaran;
        $model->state = isset($data['taskid']) ? $data['taskid'] : null;
        $model->created_date = date('Y-m-d H:i:s');
        $model->created_by = null;
        $model->payload = isset($data) ? json_encode($data) : null;
        $model->sync_respon = isset($response) ? json_encode($response) : null;
    
        $model->save();        
    }

    protected function _getInfoPendaftaran($pendaftaran_id)
    {
        $today_id = date('N') + 74;
        $dataPendaftaran = Yii::$app->db->createCommand("
            select 
                pt.pasien_id,
                pt.pegawai_id,
                pt.ruangan_id,
                pm.kode_dokter_bpjs ,
                pm.nama_pegawai  ,
                rm.kode_ruangan_bpjs ,
                rm.ruangan_nama,
	            pt.tgl_pendaftaran 
            from pendaftaran_t pt  
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id 
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id 
            where  pt.pendaftaran_id = {$pendaftaran_id};
        ")->queryOne();

        $pegawai_id = isset($dataPendaftaran['pegawai_id']) ? $dataPendaftaran['pegawai_id'] : 0 ;
        $ruangan_id = isset($dataPendaftaran['ruangan_id']) ? $dataPendaftaran['ruangan_id'] : 0;
        $tgl_pendaftaran = isset($dataPendaftaran['tgl_pendaftaran']) ? $dataPendaftaran['tgl_pendaftaran'] : 0;
        $waktu_pendaftaran = date('H:i:s', strtotime($tgl_pendaftaran));

        $jadwalDokter = Yii::$app->db->createCommand("
            select 
                d.jadwaldokter_id ,
                d.jadwaldokter_mulai ,
                d.jadwaldokter_tutup ,
                d.jumlah_loaddokter as estimasidilayani,
                d.kuota_bpjs_online + d.kuota_bpjs_offline AS kuotajkn,
                d.kuota_nonbpjs_online + d.kuota_nonbpjs_offline AS kuotanonjkn,
                kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline AS sisakuotajkn,
                kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline AS sisakuotanonjkn
            from jadwalbukapoli_m j
                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                JOIN ( SELECT a.kuota_bpjs_online,
                        a.kuota_nonbpjs_online,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r ON d.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
                JOIN ( SELECT a.kuota_bpjs_offline,
                        a.kuota_nonbpjs_offline,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r_offline ON d.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id AND kuotadokter_r_offline.is_online = false
            where j.ruangan_id = {$ruangan_id}
                and j.hari = {$today_id}
                and p.pegawai_id = {$pegawai_id}
                and d.is_deleted = false and d.is_active = true
                and p.is_deleted = false and p.is_active = true        
        ")->queryAll();
        /** Proses selected range tanggal ketika ada 2 shift */
        $endBefore = null;
        $selectedJadwal = [];

        foreach ($jadwalDokter as $key => $value) {
            if (!empty($endBefore)) {
                if (strtotime($waktu_pendaftaran) < strtotime($value['jadwaldokter_mulai'])) {
                    $selectedJadwal = $jadwalDokter[$key-1];
                    break;
                }
            }
            
            if (strtotime($waktu_pendaftaran) <= strtotime($value['jadwaldokter_mulai'])
                    || (strtotime($waktu_pendaftaran) >= strtotime($value['jadwaldokter_mulai']) 
                            && strtotime($waktu_pendaftaran) <= ($value['jadwaldokter_tutup']) )) {
                $selectedJadwal = $value;
            }
        
            $endBefore = $value['jadwaldokter_tutup'];
        }

        if (empty($selectedJadwal) && !empty($value)) {
            $selectedJadwal = $value;
        }

        $jam_mulai = !empty($selectedJadwal['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($selectedJadwal['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_tutup'])) : '00:00';

        return [
            'kodepoli' => isset($dataPendaftaran['kode_ruangan_bpjs']) ? $dataPendaftaran['kode_ruangan_bpjs'] : '-',
            'namapoli' => isset($dataPendaftaran['ruangan_nama']) ? $dataPendaftaran['ruangan_nama'] : '-',
            'kodedokter' => isset($dataPendaftaran['kode_dokter_bpjs']) ? $dataPendaftaran['kode_dokter_bpjs'] : '-',
            'namadokter' => isset($dataPendaftaran['nama_pegawai']) ? $dataPendaftaran['nama_pegawai'] : '-',
            'jampraktek' => $jam_mulai . '-' . $jam_tutup,
            'kuotajkn' => isset($selectedJadwal['kuotajkn']) ? $selectedJadwal['kuotajkn'] : 0,
            'kuotanonjkn' => isset($selectedJadwal['kuotanonjkn']) ? $selectedJadwal['kuotanonjkn'] : 0,
            'sisakuotajkn' => isset($selectedJadwal['sisakuotajkn']) ? $selectedJadwal['sisakuotajkn'] : 0,
            'sisakuotanonjkn' => isset($selectedJadwal['sisakuotanonjkn']) ? $selectedJadwal['sisakuotanonjkn'] : 0,
            'jammulai' => $jam_mulai,
            'estimasidilayani' => isset($selectedJadwal['estimasidilayani']) ? $selectedJadwal['estimasidilayani'] : 0,
        ];
    }

    private function getLastSepByRujukan($data_history_sep, $no_rujukan)
    {
        if (empty($data_history_sep)) return false;
        return ArrayHelper::getValue(array_values(array_filter($data_history_sep, function($val) use ($no_rujukan){
           return $val['noRujukan'] == $no_rujukan;
        })), '0'); // get first element
    }

    private function rollbackAntrianJkn($antrianJkn = [], $simpanAntrianJkn = [])
    {
        if (
            (new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1
            && !empty($simpanAntrianJkn)
            && !empty($antrianJkn)
            && $simpanAntrianJkn['metadata']['code'] == 200
        ) {
            $request = [
                'kodebooking' => ArrayHelper::getValue($antrianJkn, 'kodebooking'),
                'keterangan' => 'rollback create antrian jkn',
            ];

            $batal_antrian = (new BpjsJkn)->batalAntrianJkn($request);
            $this->setLogs($batal_antrian, $request, ArrayHelper::getValue($antrianJkn, 'pendaftaran_id'));
            return true;
        }

        return false;
    }

}
