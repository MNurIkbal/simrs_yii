<?php
/*
 * @Author: ardi pratama
 * @Date: 2018-11-13 10:27:50
 * @Description: BE Tindakan BMHP
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoAkunting;

use app\modules\v1\models\TarifTotalRs;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\StokObatAlkesT;
use app\modules\v1\models\CekTagihanView;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\TindakanBmhpView;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\SoapRjView;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;

use app\modules\v1\payload\ParamModel;

// integrate akunting
use SirsCore\features\IntegrasiAkunting;
use app\modules\v1\models\RuanganView;
use Doco\Traits\TindakanBmhpTrait;

class TindakanBmhpController extends DocoActiveController
{
    use TindakanBmhpTrait;

    public $konfigCpptKosong;
    public $modelClass = 'app\modules\v1\models\InfoKunjunganRajal';
    public $messageBroker = [
        'save-tindakan-bmhp' => [
            'services' => [
                'Satusehat' => [
                    'Procedure' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        
    ];

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

    public function init()
    {
        parent::init();
        // data bundle requirement
        $this->type = 'RJ';
        $this->konfigSystemCppt();
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
    }

    private function getLookupTindakanBmhpByType($type)
    {
        $result = Lookup::find()
                    ->select([
                        'lookup_id',
                        'lookup_type',
                        'lookup_name',
                        'lookup_value',
                        'lookup_urutan',
                        'lookup_kode'
                    ]);

        if ($type && is_array($type)){
            $result->where(['in','lookup_type',$type]);
        }
        $result = $result->asArray()->all();
        $list_type = [];
        if($result && count($result)>0){
            foreach ($result as $res_lookup) {
                $list_type[$res_lookup['lookup_type']][] = $res_lookup;
            }
        }

        foreach ($type as $key_tipe) {
            if(!isset($list_type[$key_tipe])){
                $list_type[$key_tipe] = [];
            }
        }

        return $list_type;
    }

    private function getPegawaiTindakanRuangan($ruangan_id = null, $kelompokpegawai = null)
    {
        $sql = "
            SELECT
                ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai
            FROM ruanganpegawai_mp
            JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id
            JOIN kelompokpegawai_m ON kelompokpegawai_m.kelompokpegawai_id = pegawai_m.kelompokpegawai_id
            WHERE ruanganpegawai_mp.is_deleted = FALSE
        ";
        if ($ruangan_id){
            $sql .= " AND ruanganpegawai_mp.ruangan_id =".$ruangan_id;
        }

        if ($kelompokpegawai){
            $sql .= " AND kelompokpegawai_m.kelompokpegawai_namalainnya ='".$kelompokpegawai."'";
        }

        $result = RuanganPegawai::findBySql($sql);

        return $result->asArray()->all();
    }

    private function getDataTindakanBmhpRuangan($no_pendaftaran,$ruangan_id)
    {
        // Check request
        if ($no_pendaftaran != null) {
            // Get data
            $data = RiwayatTindakanView::find()
                    ->where(['no_pendaftaran' => $no_pendaftaran,'ruangan_pendaftaran_id'=>$ruangan_id])
                    ->orderBy(['tgl_tindakan'=>SORT_DESC])
                    ->all();

            // Return
            return $data;
        }
        return [];
    }

    private function getObatAlkesBmhp($jenisobatalkes_id = null)
    {
        $result = ObatAlkes::find();

        if ($jenisobatalkes_id){
            $result->where(['jenisobatalkes_id' => $jenisobatalkes_id]);
        }

        return $result->asArray()->all();
    }

    /**
    * @controller actionCetakTindakan
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #inf_nopendaftaran# => Informasi Pasien: No. Pendaftaran
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #inf_dokter# => Informasi Pasien: Dokter
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
    * @attribute #inf_poliklinik# => Informasi Pasien: Poliklinik
    * @attribute #inf_statuspelayanan# => Informasi Pasien: Status Pelayanan
    * @attribute #table_tindakan_bmhp# => print
    **/
    public function actionCetakTindakan()
    {
        $request = Yii::$app->request;
        $no_pendaftaran = $request->get('no_pendaftaran',0);
        $ruangan_id = $request->get('ruangan_id',0);

        $infoKunjungan = InfoKunjunganRajal::find()->where(['no_pendaftaran'=>$no_pendaftaran])->asArray()->one();

        $print = new DocoPrint();
        $print->attributes = [
            '#inf_norekammedik#' => isset($infoKunjungan['no_rekam_medik']) ? $infoKunjungan['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => isset($infoKunjungan['tgl_pendaftaran']) ? date('d-m-Y H:i:s',strtotime($infoKunjungan['tgl_pendaftaran'])) : '',
            '#inf_nopendaftaran#' => isset($infoKunjungan['no_pendaftaran']) ? $infoKunjungan['no_pendaftaran'] : '',
            '#inf_namapasien#' => isset($infoKunjungan['nama_pasien']) ? $infoKunjungan['nama_pasien'] : '',
            '#inf_jeniskelamin#' => isset($infoKunjungan['jenis_kelamin']) ? $infoKunjungan['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => isset($infoKunjungan['jeniskasuspenyakit_nama']) ? $infoKunjungan['jeniskasuspenyakit_nama'] : '',
            '#inf_dokter#' => isset($infoKunjungan['nama_pegawai']) ? $infoKunjungan['nama_pegawai'] : '',
            '#inf_tgllahir#' => isset($infoKunjungan['tanggal_lahir']) ? date('d-m-Y',strtotime($infoKunjungan['tanggal_lahir'])) : '',
            '#inf_umur#' => isset($infoKunjungan['umur']) ? $infoKunjungan['umur'] : '',
            '#inf_kelaspelayanan#' => isset($infoKunjungan['kelaspelayanan_nama']) ? $infoKunjungan['kelaspelayanan_nama'] : '',
            '#inf_penjamin#' => isset($infoKunjungan['penjamin_nama']) ? $infoKunjungan['penjamin_nama'] : '',
            '#inf_carabayar#' => isset($infoKunjungan['carabayar_nama']) ? $infoKunjungan['carabayar_nama'] : '',
            '#inf_poliklinik#' => isset($infoKunjungan['poliklinik']) ? $infoKunjungan['poliklinik'] : '',
            '#inf_statuspelayanan#' => isset($infoKunjungan['status_periksa1']) ? $infoKunjungan['status_periksa1'] : '',
            '#table_tindakan_bmhp#' => $this->renderPartial('tindakan_bmhp',
                [
                    'data_tindakanbmhp' => $this->getDataTindakanBmhpRuangan($no_pendaftaran,$ruangan_id)
                ]
        ),
        ];
        $print->Output();
    }

    /*
    * Deprecated
    */
    public function actionHapusTindakan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->post('pendaftaran_id',0);
        $id_batal = $request->post('id_batal',0);
        $tipe = $request->post('tipe','TINDAKAN');

        try{
            $data_riwayat = RiwayatTindakanView::find()->where(['pendaftaran_id'=>$pendaftaran_id,'id'=>$id_batal,'tipe_pelayanan'=>$tipe])->one();
            $instalasi_id = Yii::$app->jwt->instalasi_id;
            
            if(is_null($data_riwayat)){
                throw new \yii\base\ErrorException("Riwayat Tindakan Tidak Ditemukan", 500);
            }
            if($tipe == 'BMHP'){
                $mBmhp = ObatAlkesPasien::find()->where(['obatalkespasien_id'=>$id_batal])->one();

                if(is_null($mBmhp)){
                    throw new \yii\base\ErrorException("Obat Tidak Ditemukan", 400);
                }
                if(isset($mBmhp->obatsudahbayar_id)){
                    throw new \yii\base\ErrorException("Obat Sudah Dibayar", 400);
                }
                if(isset($data_riwayat->pasienpulang_id)){
                    throw new \yii\base\ErrorException("Pasien Sudah Pulang", 400);
                }

                if(isset($mBmhp->tindakanpelayanan_id)){
                    $mBmhp->tindakanpelayanan_id = null;
                }
                $mBmhp->is_deleted = TRUE;
                IntegrasiAkunting::integrateRevertBmhp($id_batal, $instalasi_id, 'DELETE');
                if(!$mBmhp->update(false)){
                    throw new \yii\base\ErrorException("Gagal Hapus BMHP", 400);
                }
                $res_return_stok = StokObatAlkesT::updateAll(['is_deleted'=>TRUE],['obatalkespasien_id'=>$mBmhp->obatalkespasien_id]);

                $cekTagihan = CekTagihanView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
                if(isset($cekTagihan)){
                    $sisaTagihan = ( (float) $cekTagihan->total_tagihan ) - ( (float) $cekTagihan->total_sdh_bayar );
                    if($sisaTagihan > 0){
                        $modelPendaftaran = Pendaftaran::findOne($pendaftaran_id);

                        // Cek pendaftaran
                        if ($modelPendaftaran) {
                            // Set status bayar
                            $modelPendaftaran->status_bayar = '349';
                            $modelPendaftaran->save();
                        }
                    }
                }
                return ['message'=>'Berhasil','text'=>'Berhasil Dibatalkan','responseCode'=>200];
            }else{
                $params = [
                    'no_pendaftaran' => $data_riwayat['no_pendaftaran'],
                    'ruangan_id' => $data_riwayat['ruangan_pendaftaran_id'],
                    'tindakanpelayanan_id' => $id_batal,
                ];

                $this->batalTindakan($params);

                $mBmhp = ObatAlkesPasien::find()
                    ->where(['tindakanpelayanan_id' => $id_batal])->asArray()->all();

                if(isset($mBmhp) && count($mBmhp) > 0) {
                    foreach ($mBmhp as $v_mBmhp) {
                        $res_return_stok = StokObatAlkesT::updateAll(['is_deleted'=>TRUE],['obatalkespasien_id'=>$v_mBmhp['obatalkespasien_id']]);
                    }

                    $res_delete_bmhp = ObatAlkesPasien::updateAll(['is_deleted'=>TRUE],['tindakanpelayanan_id'=>$id_batal]);
                }

                IntegrasiAkunting::integrateRevertTindakan($id_batal, $instalasi_id, 'DELETE');
                $cekTagihan = CekTagihanView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
                if(isset($cekTagihan)){
                    $sisaTagihan = ( (float) $cekTagihan->total_tagihan ) - ( (float) $cekTagihan->total_sdh_bayar );

                    if($sisaTagihan > 0){
                        $modelPendaftaran = Pendaftaran::findOne($pendaftaran_id);
                        // Cek pendaftaran
                        if ($modelPendaftaran) {
                            // Set status bayar
                            $modelPendaftaran->status_bayar = '349';
                            $modelPendaftaran->save();
                        }
                    }
                }
                return ['message'=>'Berhasil','text'=>'Berhasil Dibatalkan','responseCode'=>200];
            }

            return ['message'=>'Gagal','text'=>'Tidak Ada yang dihapus','responseCode'=>200];
        } catch(\yii\base\ErrorException $e){
            $text_error = $e->getMessage();
            if($e->getCode() !== 400){
                $text_error = 'Terjadi Kesalahan';
            }
            return ['message'=>'Gagal','text'=>$text_error,'responseCode'=>400];
        } catch(\yii\db\Exception $e){
            return ['message'=>'Gagal','text'=>'Terjadi Kesalahan','responseCode'=>500];
        }
    }

    public function actionCreateTindakanBmhp()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                // Get post
                $post = $request->post();
                $data_tindakanpelayanan = isset($post['TindakanPelayananForm']) ? $post['TindakanPelayananForm'] : [];
                $data_bmhpalkes = isset($post['ObatAlkesPasienForm']) ? $post['ObatAlkesPasienForm'] : [];
                $data_tindakanbmhp = isset($post['TindakanBmhpForm']) ? $post['TindakanBmhpForm'] : [];
                $pendaftaran_id = $request->get('pendaftaran_id',0);
                $info = InfoKunjunganRajal::find()->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])->asArray()->one();

                if (empty($info)) {
                    throw new \yii\base\Exception("Error Processing Request", 1);
                }
                $ruangan_id = $info['ruangan_id'];
                $_POST['ruangan_id'] = $ruangan_id;

                $tindakanPelayanan = [];
                $tindakanKomponen = [];
                $list_save_tindakan = [];

                // Insert tindakan pelayanan
                foreach ($data_tindakanpelayanan as $key => $value) {

                    // Cek tipe paket
                    if (isset($value['tipepaket_id']) && $value['tipepaket_id'] != '') {
                        // Hilangkan daftar tindakan id
                        $value['daftartindakan_id'] = '';
                    }
                    $value['kelaspelayanan_id'] = $info['kelaspelayanan_id'];
                    $value['pasien_id'] = $info['pasien_id'];
                    $value['instalasi_id'] = $info['instalasi_id'];
                    $value['carabayar_id'] = $info['carabayar_id'];
                    $value['pendaftaran_id'] = $pendaftaran_id;
                    $value['jeniskasuspenyakit_id'] = $info['jeniskasuspenyakit_id'];
                    $value['ruangan_id'] = $ruangan_id;
                    $value['tgl_tindakan'] = date('Y-m-d H:i:s');
                    $value['penjamin_id'] = $info['penjamin_id'];

                    // Assign
                    $tindakanPelayanan[] = $value;
                    $mTindakan = new TindakanPelayanan;
                    $mTindakan->attributes = $value;
                    $mTindakan->dokterpenanggungjawab_id = isset($data_tindakanbmhp['dokterpenanggungjawab_id']) ? $data_tindakanbmhp['dokterpenanggungjawab_id'] : null;
                    $mTindakan->dokterdelegasi_id = isset($data_tindakanbmhp['dokterdelegasi_id']) ? $data_tindakanbmhp['dokterdelegasi_id'] : null;
                    $mTindakan->perawat1_id = isset($data_tindakanbmhp['perawat1_id']) ? $data_tindakanbmhp['perawat1_id'] : null;
                    $mTindakan->perawat2_id = isset($data_tindakanbmhp['perawat2_id']) ? $data_tindakanbmhp['perawat2_id'] : null;
                    if(!$mTindakan->validate()){
                        throw new \yii\db\Exception("Error Validate", $mTindakan->getErrors());
                    }
                    if(!$mTindakan->save()){
                        throw new \yii\db\Exception("Error Save", $mTindakan->getErrors());
                    }

                    $dataTarif = InfoTarifRs::find()->where([
                        'kelaspelayanan_id' => $mTindakan->kelaspelayanan_id,
                        'penjamin_id' => $mTindakan->penjamin_id,
                        'ruangan_id' => $mTindakan->ruangan_id
                    ]);
                    if (isset($mTindakan->tipepaket_id)) {
                        $dataTarif->andWhere(['tipepaket_id'=>$mTindakan->tipepaket_id]);
                    }else{
                        $dataTarif->andWhere(['daftartindakan_id'=>$mTindakan->daftartindakan_id]);
                    }
                    $infoTarifRs = $dataTarif->andWhere('infotarifrs_v.komponentarif_id <> 6')->asArray()->all();

                    if(count($infoTarifRs)>0){
                        foreach ($infoTarifRs as $k_infotarif => $v_infotarif) {

                            $mKomponen = new Tindakankomponen;
                            $mKomponen->komponentarif_id = $v_infotarif['komponentarif_id'];
                            $mKomponen->tindakanpelayanan_id = $mTindakan->getPrimaryKey();
                            $mKomponen->tarif_kompsatuan = $v_infotarif['harga_tariftindakan'];

                            $tarifcyto_komponen = 0;
                            if(isset($mTindakan->cyto_tindakan) && $mTindakan->cyto_tindakan == TRUE){
                                if($v_infotarif['persencyto_tindakan'] > 0){
                                    $tarifcyto_komponen = ($v_infotarif['persencyto_tindakan'] / 100 * $v_infotarif['harga_tariftindakan']);
                                }
                            }
                            $mKomponen->tarif_tindakankomp = ($v_infotarif['harga_tariftindakan'] + $tarifcyto_komponen) * $mTindakan->qty_tindakan;
                            $mKomponen->tarifcyto_tindakankomp = $tarifcyto_komponen * $mTindakan->qty_tindakan;
                            $mKomponen->subsidiasuransikomp = 0;
                            $mKomponen->subsidipemerintahkomp = 0;
                            $mKomponen->subsidirumahsakitkomp = 0;
                            $mKomponen->iurbiayakomp = 0;
                            if(!$mKomponen->save()){
                                throw new \yii\base\Exception("Error Processing Request", 1);
                            }
                        }
                    }

                    if(isset($value['daftartindakan_id']) && $value['daftartindakan_id'] != ''){
                        if(isset($mTindakan->cyto_tindakan) && $mTindakan->cyto_tindakan == TRUE){
                            $list_save_tindakan['tindakan']['cyto'][$value['daftartindakan_id']] = $mTindakan->getPrimaryKey();
                        }else{
                            $list_save_tindakan['tindakan']['noncyto'][$value['daftartindakan_id']] = $mTindakan->getPrimaryKey();
                        }
                    }else if(isset($value['tipepaket_id']) && $value['tipepaket_id'] != ''){
                        if(isset($mTindakan->cyto_tindakan) && $mTindakan->cyto_tindakan == TRUE){
                            $list_save_tindakan['paket']['cyto'][$value['tipepaket_id']] = $mTindakan->getPrimaryKey();
                        }else{
                            $list_save_tindakan['paket']['noncyto'][$value['tipepaket_id']] = $mTindakan->getPrimaryKey();
                        }
                    }
                }

                // Cek pendaftaran id
                if ($pendaftaran_id != '') {
                    $modelPendaftaran = Pendaftaran::findOne($pendaftaran_id);
                    if ($modelPendaftaran) {
                        $modelPendaftaran->status_bayar = '349';
                        $modelPendaftaran->save();
                    }
                }

                if (!empty($data_bmhpalkes)) {
                    $sisa = 0;
                    $detailTrans = [];
                    foreach ($data_bmhpalkes as $key => $val_bmhp) {
                        // Declare model
                        $model = new ObatAlkesPasien();
                        $idObatAlkes = $val_bmhp['obatalkes_id'];
                        $tagihkan = $val_bmhp['is_ditagihkan'] == 1 ? true : false;
                        $infoObat = InfoStokObatAlkesFn::find()->select(['obatalkes_nama', 'obatalkes_id', 'ruangan_id', 'obatalkes_kode', 'qty_tersedia','satuankecil_id', 'satuankecil_nama', 'hargaygdipakai', 'harganetto_ygdipakai', 'jml_margin as jmlmargin', 'jml_discount as jmldiscount', 'jml_ppn as jmlppn', 'persen_ppn as persenppn', 'persen_disc as persendiscount', 'persen_margin as persenmargin'])->where([
                                'ruangan_id' => $ruangan_id,
                                'obatalkes_id' => $idObatAlkes
                            ])->asArray()->one();
                        if (empty($infoObat)) {
                            return [
                                'title' => 'Proses Gagal',
                                'text' => 'Obat tidak ditemukan',
                                'status' => 422
                            ];
                        }
                        $idTindakan = null;
                        $infoTindakan = new TindakanPelayanan;
                        if(isset($val_bmhp['tipepaket_id']) && $val_bmhp['tipepaket_id'] != ''){
                            $is_paket_tindakan = true;
                            $model->tipepaket_id = $val_bmhp['tipepaket_id'];
                            if(isset($val_bmhp['tindakan_is_cyto']) && $val_bmhp['tindakan_is_cyto'] == 1){
                                if(isset($list_save_tindakan['paket']['cyto'][$val_bmhp['tipepaket_id']])){
                                    $idTindakan = $list_save_tindakan['paket']['cyto'][$val_bmhp['tipepaket_id']];
                                }
                            }else{
                                if(isset($list_save_tindakan['paket']['noncyto'][$val_bmhp['tipepaket_id']])){
                                    $idTindakan = $list_save_tindakan['paket']['noncyto'][$val_bmhp['tipepaket_id']];
                                }
                            }
                        }else if(isset($val_bmhp['daftartindakan_id']) && $val_bmhp['daftartindakan_id'] != ''){
                            $is_paket_tindakan = false;
                            $model->daftartindakan_id = $val_bmhp['daftartindakan_id'];
                            if(isset($val_bmhp['tindakan_is_cyto']) && $val_bmhp['tindakan_is_cyto'] == 1){
                                if(isset($list_save_tindakan['tindakan']['cyto'][$val_bmhp['daftartindakan_id']])){
                                    $idTindakan = $list_save_tindakan['tindakan']['cyto'][$val_bmhp['daftartindakan_id']];
                                }
                            }else{
                                if(isset($list_save_tindakan['tindakan']['noncyto'][$val_bmhp['daftartindakan_id']])){
                                    $idTindakan = $list_save_tindakan['tindakan']['noncyto'][$val_bmhp['daftartindakan_id']];
                                }
                            }
                        }
                        if (!empty($idTindakan)) {
                            $infoTindakan = TindakanPelayanan::find()->where([
                                'tindakanpelayanan_id' => $idTindakan
                            ])->asArray()->one();
                        }
                        $model->tindakanpelayanan_id = $idTindakan;
                        $model->obatalkes_id = $idObatAlkes;
                        $model->stok_obat = isset($infoObat['qty_tersedia']) ? $infoObat['qty_tersedia'] : 0;
                        $model->perawat1_id = isset($data_tindakanbmhp['perawat1_id']) ? $data_tindakanbmhp['perawat1_id'] : null;
                        $model->perawat2_id = isset($data_tindakanbmhp['perawat2_id']) ? $data_tindakanbmhp['perawat2_id'] : null;
                        $model->ruangan_id = isset($ruangan_id) ? $ruangan_id : null;
                        $model->carabayar_id = isset($info['carabayar_id']) ? $info['carabayar_id'] : null;
                        $model->pegawai_id = isset($info['pegawai_id']) ? $info['pegawai_id'] : null;
                        $model->satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
                        $model->pendaftaran_id = $pendaftaran_id;
                        $model->pasien_id = isset($info['pasien_id']) ? $info['pasien_id'] : null;
                        $model->penjamin_id = isset($info['penjamin_id']) ? $info['penjamin_id'] : null;
                        $model->kelaspelayanan_id = isset($info['kelaspelayanan_id']) ? $info['kelaspelayanan_id'] : null;
                        $model->tglpelayanan = date('Y-m-d H:i:s');
                        $model->qty_oa = $val_bmhp['qty_oa'];
                        $model->hargasatuan_oa = isset($infoObat['hargaygdipakai']) && $tagihkan ? $infoObat['hargaygdipakai'] : 0;
                        $model->harganetto_oa = isset($infoObat['harganetto_ygdipakai']) && $tagihkan ? $infoObat['harganetto_ygdipakai'] : 0;
                        $model->hargajual_oa = $tagihkan ? $model->qty_oa * $model->hargasatuan_oa : 0;
                        if ($model->validate() && $model->save()) {
                            $tanggalBerlaku = date('Y-m-d');
                            // Mencari Metode
                            $konfig = $connection->createCommand("
                                SELECT metodeantrian FROM konfigfarmasi_k
                                WHERE tglberlaku >= '{$tanggalBerlaku}'
                                AND konfigfarmasi_aktif = true
                                AND is_active = true
                            ")->queryOne();
                            // Mencari Metode dengan nilai default FEFO
                            $currentMetode = LogicStokObatAlkes::FEFO;
                            if ($konfig) {
                                $currentMetode = isset($konfig['metodeantrian'])
                                                    ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                            }
                            $detailTrans[] = [
                                'obatalkes_id' => $idObatAlkes,
                                'qty_satuanpakai' => $val_bmhp['qty_oa'],
                                'satuankecil_id' => isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null,
                                'obatalkespasien_id' => $model->obatalkespasien_id,
                                'harganetto' => $infoObat['harganetto_ygdipakai'],
                                'jmlppn' => $infoObat['jmlppn'],
                                'jmlmargin' => $infoObat['jmlmargin'],
                                'jmldiscount' => $infoObat['jmldiscount'],
                                'persendiscount' => $infoObat['persendiscount'],
                                'persenmargin' => $infoObat['persenmargin'],
                                'persenppn' => $infoObat['persenppn'],
                            ];
                        }else{
                            throw new \Exception("Terjadi Kesalahan", 1);
                        }
                    }
                    if(count($detailTrans) > 0){
                        $tanggalPemakaian = date('Y-m-d H:i:s');
                        // Execute By Condition
                        LogicStokObatAlkes::$distribusi = false;
                        if ($currentMetode === LogicStokObatAlkes::FEFO) {
                           $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalPemakaian);
                        } else {
                           $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalPemakaian);
                        }
                    }
                }
                $status = DocoConstants::BELUM_LUNAS;
                Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET status_bayar = {$status}
                    WHERE pendaftaran_id = {$pendaftaran_id}
                ")->execute();
                $transaction->commit();
                
                $no_pendaftaran = $modelPendaftaran->no_pendaftaran;
                $instalasi_id = Yii::$app->jwt->instalasi_id;

                IntegrasiAkunting::integrateTindakanBmhp($no_pendaftaran, $instalasi_id);

                return ['message' => 'Data Berhasil di simpan', 'pendaftaran_id' => $pendaftaran_id];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;

            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function integrateBmhp($pendaftaran_id)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $resep = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'jenis' => 'BMHP', 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->jenisobatalkes_kode,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xamount_netto' => $value->harga_netto,
                            'xamount_ppn' => $value->jmlppn,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => $value->is_ditagihkan,
                        ];
                        $obatalkes = ObatAlkesPasien::findOne($value->id);
                        $obatalkes->is_jurnal = true;
                        $obatalkes->scenario = "jurnal";
                        $obatalkes->update();
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                return true;
                // throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateTindakan($pendaftaran_id)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($tindakan)){
                    foreach($tindakan as $key => $value){
                        $harga = $value->harga;
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->komponentarif_kode,
                            'xtransaction_at' => $value->tanggal_transaksi,
                            'xamount' => $harga,
                            'xdiscount_amount' => $value->diskon,
                            'xamount_netto' => 0,
                            'xamount_ppn' => 0,
                            'xmedical_number' => $value->rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => 'DITAGIHKAN',
                        ];
                        $Tindakankomponen = Tindakankomponen::findOne($value->id);
                        $Tindakankomponen->is_jurnal = true;
                        $Tindakankomponen->update();
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                return true;
                // throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhpCrud($pendaftaran_id, $method)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            
            $resep = SyncPengeluaranobat::find()
            ->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'jenis' => 'BMHP'])
            ->andWhere(['instalasi_id' => $pendaftaran->instalasi_id])
            ->all();
            // var_dump($resep);
            // die();

            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
                        $data[] = [
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xcategori_code' => $value->jenisobatalkes_kode, 
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xamount_netto' => $value->harga_netto,
                            'xamount_ppn' => $value->jmlppn,
                            'xref_number_id' => $value->id,
                            'xis_billing' => $value->is_ditagihkan,
                            'xcrud' => $method,
                        ];
                    }
                    $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                    // return $var;
                }
            } else {
                return true;
                // throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhpCrudt($pendaftaran_id, $method)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            
            $tindakan = SyncTindakan::find()
            ->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran])
            ->andWhere(['instalasi_id' => $pendaftaran->instalasi_id])
            ->all();
            // var_dump($tindakan);
            // die();

            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                if(!empty($tindakan)){
                    foreach($tindakan as $key => $value){
                        $harga = $value->harga;
                        $data[] = [
                           'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->komponentarif_kode,
                            'xtransaction_at' => $value->tanggal_transaksi,
                            'xamount' => $harga,
                            'xdiscount_amount' => $value->diskon,
                            'xamount_netto' => 0,
                            'xamount_ppn' => 0,
                            'xmedical_number' => $value->rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => 'DITAGIHKAN',
                            'xcrud' => $method,
                        ];
                    }
                    $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                    // return $var;
                }
            } else {
                return true;
                // throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionSaveTindakanBmhp()
    {
        return Yii::$app->docoPlugin->execute('tindakan_bmhp');
    }

    public function actionHapusTindakanBmhp()
    {
        return Yii::$app->docoPlugin->execute('tindakan_bmhp_hapus');
    }

    public function actionHistoryPembatalan()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $tindakanpelayanan_id = $request->get('tindakanpelayanan_id', null);
            $obatalkespasien_id = $request->get('obatalkespasien_id', null);
            $permintaankepenunjang_id = $request->get('permintaankepenunjang_id', null);
            $no_resep = $request->get('noresep', null);
            $whereId = [];

            if(!is_null($obatalkespasien_id)){
                $whereId = ['obatalkespasien_id'=>$obatalkespasien_id];
            }

            if(!is_null($tindakanpelayanan_id)){
                $whereId = ['tindakanpelayanan_id'=>$tindakanpelayanan_id];
            }

            if(!is_null($no_resep)) {
                $whereId = ['noresep'=>$no_resep];
            }

            if(!is_null($permintaankepenunjang_id)) {
                $whereId = ['permintaankepenunjang_id'=>$permintaankepenunjang_id];
            }


            $soapRj = SoapRjView::find(true)
                ->select(['tgl_batal', 'pegawai_hapus_nama', 'alasan_batal', 'jenis as tipe_instruksi', 'qty'])
                ->where(['pendaftaran_id' => $pendaftaran_id]);
            
            if(!empty($whereId)) {
                $soapRj->andWhere($whereId);
            }

            return $soapRj->one();
            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        }
    }

    private function batalTindakan($params)
    {
        $no_pendaftaran = isset($params['no_pendaftaran']) ? $params['no_pendaftaran'] : null;
        $ruangan_id = isset($params['ruangan_id']) ? $params['ruangan_id'] : null;
        $tindakanpelayanan_id = isset($params['tindakanpelayanan_id']) ? $params['tindakanpelayanan_id'] : null;

        $param = [
            'no_pendaftaran' => $no_pendaftaran,
            'ruangan_id' => $ruangan_id,
        ];
        
        $param['detail_tindakan'] = ['tindakanpelayanan_id' => $tindakanpelayanan_id];
        $restKasir = Yii::$app->docoRest->kasir;
        $request = $restKasir->post('api/batal-tagihan', [
            'form_params' => $param
        ]);
    }
}
