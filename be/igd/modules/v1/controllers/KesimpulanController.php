<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use yii\db\Expression;
use yii\helpers\Html;

use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\KondisiKeluar;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\KesimpulanRD;
use app\modules\v1\models\Pendaftaran;
use Doco\models\Pegawai;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\InfoPasienRi;
use app\modules\v1\models\PasienPulangRdRiView;
use app\modules\v1\models\KesimpulanRdView;
use app\modules\v1\models\GantiDokterPj;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KonfigAntrianView;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\Triase;
use app\modules\v1\models\AsesmenPerawatRD;

use app\modules\v1\models\PersetujuanJenazah;
use app\modules\v1\models\InfoPasienMeninggal;
use app\modules\v1\models\InfoPasienMeninggalDetail;
use app\modules\v1\models\InfoPasienMeninggalDetail2;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\ProfilRumahSakitM;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\RujukanPulang;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\InfoPasienPulangRjRd;
use app\modules\v1\models\CpptRjV;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\InfoInstruksiView;
use Doco\components\DocoConstansId;
use Doco\models\bpjs\Bpjs;
use Doco\Services\UpdateEklaimService;

// Class
class KesimpulanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PasienPulang';

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

    public function actionBundleDataKesimpulan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        try {
            $bundle = [];
            $dataJenazah = [];
            $bundle['cara_keluar'] = CaraKeluar::find()->where(['is_active'=>true])->orderBy(['carakeluar_urutan' => SORT_ASC])->asArray()->all();
            $bundle['gcs'] = Gcs::find()->andWhere(['is_active'=>true])->orderBy(['gcs_nilaimin' => SORT_ASC])->asArray()->all();
            $bundle['metodegcs'] = MetodeGcs::find()->select(['metodegcs_id','metodegcs_nama','metodegcs_singkatan','metodegcs_nilai','CONCAT(metodegcs_nama,\' - \',metodegcs_nilai) AS metodegcs_namalengkap'])->andWhere(['is_active'=>true])->asArray()->all();
            $bundle['kondisi_keluar'] = KondisiKeluar::find()->andWhere(['is_active'=>true])->asArray()->all();
            $listDokterSpesialis = [];
            $get_konfig_keramat = LookupTransaksi::find()->select([
                'additional_value'
            ])
            ->where(['kode_transaksi' => 'keramat_spri'])
            ->asArray()->one();
            $konfig_keramat_spri = false;
            if(isset($get_konfig_keramat)){
                $konfig_keramat_spri = json_decode($get_konfig_keramat['additional_value']);
            }

            $query = InfoInstruksiView::find()
            ->select(['tipe_instruksi', 'grouping_tipe', 'tindakaninstruksi_nama', 'tgl_instruksi'])
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['tgl_instruksi' => SORT_ASC]);

            $listInstruksi = $query->asArray()->distinct()->all();
            $catatan_tindakan = "";
            $no = 0;
            foreach ($listInstruksi as $instruksi) {
                if($instruksi['tipe_instruksi'] == 'TINDAKAN' ||  $instruksi['tipe_instruksi'] == 'BMHP' || $instruksi['grouping_tipe'] == 'PENUNJANG') {
                        $no++;
                        $catatan_tindakan .= $no.". Pemeriksaan - " . $instruksi['tindakaninstruksi_nama'] . "\n";
                    }
            }

            $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
            $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
            $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
            $data_listgcs = [];
            $data_obat = $this->getInfoResepturDetail($pendaftaran_id);
            foreach ($bundle['metodegcs'] as $key => $value) {
                if (!$value['metodegcs_nilai']){
                    continue;
                }

                $value['nama_and_nilai'] = $value['metodegcs_nama'] . ' - ' . $value['metodegcs_nilai'];
                if ($value['metodegcs_singkatan'] == $gcsindicator_eye){
                    $data_listgcs['eye'][] = $value;
                }elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal){
                    $data_listgcs['verbal'][] = $value;
                }elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik){
                    $data_listgcs['motorik'][] = $value;
                }
            }

            $pasienPulang = PasienPulang::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            $kesimpulan = KesimpulanRD::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            $list_data_apotek = $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A);
            $list_data_signa = $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()->where(['is_active'=>true]));
            $listhubungan = $this->getOrSetCache('cache_hubungan_keluarga', Lookup::find()->where(['lookup_type' => 'hubungan_keluarga'])->andWhere(['is_deleted' => false, 'is_active' => true])->orderBy(['lookup_urutan' => SORT_ASC]), true);
            $listJenisKelamin = $this->getOrSetCache('cache_jenis_kelamin', Lookup::find()->where(['lookup_type' => 'jenis_kelamin'])->andWhere(['is_deleted' => false, 'is_active' => true])->orderBy(['lookup_urutan' => SORT_ASC]), true);

            $getSuggestGcs = AsesmenMedisRD::find()
                ->select([
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'hasil_gcs',
                    'is_kapitis',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            if(empty($getSuggestGcs['gcseye_id']) && empty($getSuggestGcs['gcsverbal_id']) && empty($getSuggestGcs['gcsmotorik_id']) && empty($getSuggestGcs['hasil_gcs']) ){
            $getSuggestGcs = AsesmenPerawatRD::find()
                ->select([
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'hasil_gcs',
                    'is_kapitis',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            }
            if(empty($getSuggestGcs['gcseye_id']) && empty($getSuggestGcs['gcsverbal_id']) && empty($getSuggestGcs['gcsmotorik_id']) && empty($getSuggestGcs['hasil_gcs']) ){
            $getSuggestGcs = Triase::find()
                ->select([
                    'gcseye_id',
                    'gcsverbal_id',
                    'gcsmotorik_id',
                    'hasil_gcs',
                    'is_kapitis',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            }

            $getSuggestTtv = AsesmenMedisRD::find()
                ->select([
                    'nadi as hr',
                    'pernapasan as rr',
                    'suhu as t',
                    'saturasi_o2 as spo2',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            if(empty($getSuggestTtv['hr']) && empty($getSuggestTtv['rr']) && empty($getSuggestTtv['t']) && empty($getSuggestTtv['spo2']) ){
            $getSuggestTtv = AsesmenPerawatRD::find()
                ->select([
                    'detak_nadi as hr',
                    'pernapasan as rr',
                    'suhu_tubuh as t',
                    'spo2 as spo2',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            }
            if(empty($getSuggestTtv['hr']) && empty($getSuggestTtv['rr']) && empty($getSuggestTtv['t']) && empty($getSuggestTtv['spo2']) ){
            $getSuggestTtv = Triase::find()
                ->select([
                    'nadi as hr',
                    'nafas as rr',
                    'suhu as t',
                    'saturasi_oksigen as spo2',
                ])
                ->where([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            }
            $result = [
                'bundle' => $bundle,
                'data_listgcs' => $data_listgcs,
                'pasienPulang' => $pasienPulang,
                'kesimpulan' => $kesimpulan,
                'listDataApotek' => isset($list_data_apotek) && is_array($list_data_apotek) ? $list_data_apotek : [],
                'listDataSigna' => isset($list_data_signa) && is_array($list_data_signa) ? $list_data_signa : [],
                'getSuggestGcs' => $getSuggestGcs,
                'getSuggestTtv' => $getSuggestTtv,
                'listhubungan' => $listhubungan,
                'data_obat'    => $data_obat,
                'listjk' => [
                    ['lookup_id' => DocoConstants::VAR_LK, 'lookup_value' => 'Laki - Laki'],
                    ['lookup_id' => DocoConstants::VAR_PR, 'lookup_value' => 'Perempuan'],
                ],
                'datajenazah' => $dataJenazah,
                'konfig_keramat_spri' => $konfig_keramat_spri,
                'catatan_tindakan' => $catatan_tindakan,
            ];

            return $result;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e){
            return [];
        }

    }

    private function getRuanganInstalasi($instalasi_singkatan = null)
    {
        // Try catch
        try {
            // Sql
            $sql = "
                SELECT DISTINCT
                    *
                FROM
                    ruangan_m
                LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE ruangan_m.is_deleted = FALSE
                AND ruangan_m.is_active = TRUE
            ";

            if ($instalasi_singkatan){
                $sql .= " AND instalasi_m.instalasi_singkatan = '". $instalasi_singkatan ."'";
            }

            // Result
            $result = Ruangan::findBySql($sql);

            // Return result
            return $result;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSavePasienPulang()
    {
        $request = Yii::$app->request;
        try{
            $datapost = $request->post('PasienPulangForm',[]);
            if(count($datapost) == 0){
                throw new \yii\base\Exception("Error Processing Request", 1);
            }
            $pendaftaran_id = $datapost['pendaftaran_id'];
            $model = PasienPulang::find()->where(['pendaftaran_id'=>$datapost['pendaftaran_id']])->one();
            $isNew = false;
            if(is_null($model)){
                $model = new PasienPulang;
                $isNew = true;
            }
            $model->attributes = $datapost;
            if(isset($datapost['tgl_meninggal']) && $datapost['tgl_meninggal'] != ''){
                $model->is_meninggal = true;
            }else{
                $model->is_meninggal = false;
            }

            if(!$model->validate()){
                \Yii::$app->response->statusCode = 422;
                return [
                    'data' => $model->errors,
                    'status' => 422,
                ];
            }

            if($isNew){
                if(!$model->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }
            }else{
                if(!$model->update()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }
            }

            $mPendaftaran = Pendaftaran::findOne($pendaftaran_id);
            $mPendaftaran->pasienpulang_id = $model->pasienpulang_id;
            $mPendaftaran->tgl_selesaiperiksa = date('Y-m-d H:i:s');
            $mPendaftaran->update();

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionViewKesimpulan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        try {
            $kesimpulan = KesimpulanRdView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->asArray()->one();
            $dataJenazah = $this->getPersetujuanJenazah($pendaftaran_id);
            $data_obat = $this->getInfoResepturDetail($pendaftaran_id);

            return ['kesimpulan'=>$kesimpulan, 'datajenazah' => $dataJenazah, 'obat_pulang' => $data_obat];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionSaveKesimpulan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;

        try{
            $dPasienPulang = $request->post('PasienPulangForm');
            $pendaftaran_id = $dPasienPulang['pendaftaran_id'];
            $infoPasien = InfoPasienRdV::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            if(is_null($infoPasien)){
                throw new \yii\base\Exception("Error Processing Request", 1);
            }

            $mPasienPulang = PasienPulang::find()->where(['pendaftaran_id'=>$dPasienPulang['pendaftaran_id']])->one();
            $isNew = false;
            if(is_null($mPasienPulang)){
                $mPasienPulang = new PasienPulang;
                $isNew = true;
            }

            $t_sep_new = [];
            if($dPasienPulang['nosep'] != null) {
                    $carakeluarBpjs = (new DocoConstansId)->actionGetAdditional('cara_pulang_bpjs',true);
                    $caraPulangBpjs = isset($carakeluarBpjs[$dPasienPulang['carakeluar_id']]) ? $carakeluarBpjs[$dPasienPulang['carakeluar_id']] : 5;
                    $model = new Bpjs();
    
                    $t_sep_new['noSep'] = $dPasienPulang['nosep'];
                    $t_sep_new['statusPulang'] = $caraPulangBpjs;
                    $t_sep_new['noSuratMeninggal'] = $dPasienPulang['carakeluar_id'] == 4 ? $dPasienPulang['no_surat_kematian'] : '';
                    $t_sep_new['tglMeninggal'] = $dPasienPulang['carakeluar_id'] == 4 ?  date('Y-m-d', strtotime($dPasienPulang['tgl_meninggal'])) : '';
                    $t_sep_new['tglPulang'] = date('Y-m-d', strtotime($dPasienPulang['tglpasienpulang']));
                    $t_sep_new['noLPManual'] = '';
                    $t_sep_new['user'] = $dPasienPulang['user'];
                    $model->t_sep = $t_sep_new;
                    // yii::error($t_sep_new);exit;
    
                    if ($t_sep_new['noSep'] != null && $t_sep_new['tglPulang'] != null) {
                        $result = $model->updateTanggalPulangSepNew();
                    }
            }

            $mPasienPulang->attributes = $dPasienPulang;

            $mPasienPulang->tglpasienpulang = ($mPasienPulang->tglpasienpulang) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tglpasienpulang)) : date('Y-m-d H:i:s') ;
            $mPasienPulang->tgl_meninggal = ($mPasienPulang->tgl_meninggal) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tgl_meninggal)) : '' ;

            //Perubahan format tanggal tgl_kremasi dan waktu_pemeriksaan_jenazah
            $mPasienPulang->tgl_kremasi = ($mPasienPulang->tgl_kremasi) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tgl_kremasi)) : '' ;
            $mPasienPulang->waktu_pemeriksaan_jenazah = ($mPasienPulang->waktu_pemeriksaan_jenazah) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->waktu_pemeriksaan_jenazah)) : '' ;

            if(!$mPasienPulang->validate()){
                \Yii::$app->response->statusCode = 422;
                return [
                    'data' => $mPasienPulang->errors,
                    'status' => 422,
                ];
            }

            if(!$mPasienPulang->save()){
                \Yii::$app->response->statusCode = 422;
                return [
                    'data' => $mPasienPulang->errors,
                    'status' => 422,
                ];
            }

            if(!empty($request->post('RujukanPulangForm'))){
                $mRujukanKeluar = RujukanPulang::find()->where(['pendaftaran_id' => $dPasienPulang['pendaftaran_id']])->one();
                if (is_null($mRujukanKeluar)) {
                    $mRujukanKeluar = new RujukanPulang();
                }
                $mRujukanKeluar->attributes = $request->post('RujukanPulangForm');
                if(!$mRujukanKeluar->validate()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mRujukanKeluar->errors,
                        'status' => 422,
                    ];
                }
                if(!$mRujukanKeluar->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mRujukanKeluar->errors,
                        'status' => 422,
                    ];
                }
            }

            $mKesimpulan = KesimpulanRD::find()->where(['pendaftaran_id'=>$dPasienPulang['pendaftaran_id']])->one();
            $isNewKesimpulan = false;
            if(is_null($mKesimpulan)){
                $mKesimpulan = new KesimpulanRD;
                $isNewKesimpulan = true;
            }

            $mKesimpulan->pasienpulang_id = $mPasienPulang->getPrimaryKey();

            $dKeluar = $request->post('KesimpulanKeluarForm',false);
            if($dKeluar !== false){
                $mKesimpulan->attributes = $dKeluar;
                if(!$mKesimpulan->validate()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
                if(!$mKesimpulan->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
            }
            $dPulang = $request->post('KesimpulanPulangForm',false);
            if($dPulang !== false){
                $mKesimpulan->attributes = $dPulang;
                if(!$mKesimpulan->validate()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
                if(!$mKesimpulan->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
            }
            if($dKeluar === false && $dPulang === false){
                $mKesimpulan->pendaftaran_id = $dPasienPulang['pendaftaran_id'];
                $mKesimpulan->pasienpulang_id = $mPasienPulang->getPrimaryKey();
                if(!$mKesimpulan->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
            }

            $lookup = Lookup::findOne(DocoConstants::VAR_FA_NR);
            $const = isset($lookup['lookup_value']) ? $lookup['lookup_value'] : '';
            $reseptur = $request->post('ResepturForm',false);
            if($reseptur !== false){
                $mReseptur = new Reseptur;
                $mReseptur->ruangan_id = @$reseptur['ruangan_id'];
                $mReseptur->pasien_id = @$infoPasien['pasien_id'];
                $mReseptur->pegawai_id = @$reseptur['pegawai_id'];
                $mReseptur->pendaftaran_id = $pendaftaran_id;
                $mReseptur->tglreseptur = date('Y-m-d H:i:s');
                $mReseptur->ruanganreseptur_id = $infoPasien['ruangan_id'];
                $mReseptur->status_reseptur = 346;

                $data_konfigantrianfarmasi = KonfigAntrianView::find()->where(['lookup_value'=>$const, 'ruangan_id' => $reseptur['ruangan_id'], 'is_default' => true])->one();
                $modelAntrian = new Antrian;
                $modelAntrian->ruangan_id = $mReseptur->ruangan_id;
                $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
                $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
                $modelAntrian->racikan_id = 1;
                $fungsiantrian_id = isset($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;

                $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
                $modelAntrian->save(false);
                $antrian_id = $modelAntrian->antrian_id;

                $mReseptur->antrian_id = $antrian_id;
                if(!$mReseptur->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mReseptur->errors,
                        'status' => 422,
                    ];
                }
                $mKesimpulan->reseptur_id = $mReseptur->getPrimaryKey();
                if(!$mKesimpulan->update()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mKesimpulan->errors,
                        'status' => 422,
                    ];
                }
            }
            $reseptur_detail = $request->post('ResepturDetail',false);
            if($reseptur !== false && $reseptur_detail !== false && is_array($reseptur_detail)){
                foreach ($reseptur_detail as $k_resep_detail => $v_resep_detail) {
                    $infoObat = InfoStokObatAlkesView::find()->where([
                        'ruangan_id' =>$mReseptur->ruangan_id,
                        'obatalkes_id' => $v_resep_detail['dt_obatalkes_id']
                    ])->asArray()->one();
                    if(empty($infoObat)){
                        throw new \yii\base\Exception("Data Obat Tidak Ditemukan", 1);
                    }

                    $mResepturDetail = new ResepturDetail;
                    $mResepturDetail->obatalkes_id = isset($v_resep_detail['dt_obatalkes_id']) ? $v_resep_detail['dt_obatalkes_id'] : null;
                    $mResepturDetail->rke = isset($v_resep_detail['dt_rke']) ? $v_resep_detail['dt_rke'] : null;
                    $mResepturDetail->racikan_id = isset($v_resep_detail['dt_racikan_id']) ? $v_resep_detail['dt_racikan_id'] : null;
                    $mResepturDetail->satuankecil_id = isset($v_resep_detail['dt_satuankecil_id']) ? $v_resep_detail['dt_satuankecil_id'] : null;
                    $mResepturDetail->reseptur_id = $mReseptur->getPrimaryKey();

                    // $ket_r =
                    // r
                    $mResepturDetail->rke = isset($v_resep_detail['dt_rke']) ? $v_resep_detail['dt_rke'] : null;
                    // kekuatan_reseptur
                    // satuankekuatan
                    $mResepturDetail->qty_reseptur = isset($v_resep_detail['dt_qty_reseptur']) ? $v_resep_detail['dt_qty_reseptur'] : null;
                    $mResepturDetail->hargasatuan_reseptur = @$infoObat['hargaygdipakai'];
                    $mResepturDetail->harganetto_reseptur = $infoObat['harganetto'];
                    $mResepturDetail->hargajual_reseptur = (int) $v_resep_detail['dt_qty_reseptur'] * @$infoObat['hargaygdipakai'];
                    $mResepturDetail->iter = isset($reseptur['iter']) ? $reseptur['iter'] : null;
                    $mResepturDetail->signa_id = isset($v_resep_detail['dt_signa_id']) ? $v_resep_detail['dt_signa_id'] : null;
                    // status_implementasi
                    $mResepturDetail->tgl_resepturdetail = date('Y-m-d H:i:s');
                    if(!$mResepturDetail->save()){
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'data' => $mResepturDetail->errors,
                            'status' => 422,
                        ];
                    }
                }
            }

            $mPendaftaran = Pendaftaran::findOne($pendaftaran_id);
            $mPendaftaran->pasienpulang_id = $mPasienPulang->getPrimaryKey();
            $mPendaftaran->tgl_selesaiperiksa = date('Y-m-d H:i:s');
            // 4/433
            $status_periksa = "4";
            if($mPasienPulang->carakeluar_id == 5){
                $status_periksa = "433";
            }
            $mPendaftaran->status_periksa = $status_periksa;
            if( !$mPendaftaran->save() ){
                \Yii::$app->response->statusCode = 422;
                return [
                    'data' => $mPendaftaran->errors,
                    'status' => 422,
                ];
            }


            if($infoPasien['dokter_id'] === null){
                $mDokterPj = new GantiDokterPj;
                $mDokterPj->pendaftaran_id = $pendaftaran_id;
                $mDokterPj->ruangan_id = $infoPasien['ruangan_id'];
                $mDokterPj->jenis_dokter = 485;
                $mDokterPj->dokterlama_id = $infoPasien['dokter_jaga_id'];
                $mDokterPj->dokterbaru_id = $infoPasien['dokter_jaga_id'];
                $mDokterPj->tgl_perubahan = date('Y-m-d H:i:s');
                if(!$mDokterPj->save()){
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $mDokterPj->errors,
                        'status' => 422,
                    ];
                }
            }
            $result = ['response'=> ['title' => 'Simpan Berhasil', 'message' => 'Simpan Berhasil']];
            if($dPasienPulang['carakeluar_id'] == 4 &&  ($request->post('JenazahForm', null) != null) ){
                $response = Yii::$app->docoRest->jenazah->post('order/create', [
                    'query' => [ 'id' => $dPasienPulang['pendaftaran_id'] ],
                    'form_params' => $request->post('JenazahForm')
                    // 'form_params' => $post['JenazahForm']
                ]);
                $body = json_decode($response->getBody(), true);
                if($body['metadata']['status'] != 200){
                    throw new \Exception(json_encode($body), 1);
                }
                $result = $body['response'];
            }

            $getTriase = \app\modules\v1\models\Triase::find()->select([
                'kamartempattidur_id',
                'triase_id',
                'pendaftaran_id'
            ])->where([
                'pendaftaran_id' => $mKesimpulan->pendaftaran_id
            ])->asArray()->one();
            if ( !empty($getTriase) && isset($getTriase['kamartempattidur_id']) && !empty($getTriase['kamartempattidur_id']) ) {
                $updateKamarTempatTidur = \app\modules\v1\models\KamarTempatTidur::updateAll([
                    'status_isi' => false
                ], 'kamartempattidur_id = :kamartempattidur_id', [
                    ':kamartempattidur_id' => $getTriase['kamartempattidur_id']
                ]);

                if ( !$updateKamarTempatTidur ) {
                    $transaction->rollBack();
                    Yii::error([
                        'error-msg' => 'update status kamar tempat tidur gagal'
                    ]);
                    return $this->response(422, 'Pemulangan Pasien Gagal!');
                }

                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'ketersediaan-bed-'.Yii::$app->params['mode'],
                    'message' => json_encode(['kamartempattidur_id'=>$getTriase['kamartempattidur_id'], 'status_isi'=>false]),
                ]);
            }
            $transaction->commit();

            /* update tgl pulang sync eklaim BPJS - Pemulangan Rawat Darurat */
            if(isset($infoPasien['no_pendaftaran'])) {
                $registration = [
                    'no_pendaftaran' => $infoPasien['no_pendaftaran'],
                    'instalasi_kode' => [DocoConstants::INSTALASI_RAWAT_DARURAT]
                ];

                (new UpdateEklaimService)->updateTglPulang($registration);
            }

            return $result;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCheckDiagnosa($id)
    {
        $is_diagnosa = false;
        $diagnosa_dokter = (new \yii\db\Query())
            ->select(['diagnosakerja_id'])
            ->from('asesmenmedisrd_t')
            ->where(['pendaftaran_id'=>$id])
            ->scalar();
        if($diagnosa_dokter !== false && $diagnosa_dokter !== null){
            return ['is_diagnosa_terisi'=>true];
        }

        $diagnosa_cppt = (new \yii\db\Query())
            ->select(['a_diag_utama'])
            ->from('cppt_t')
            ->where(['pendaftaran_id'=>$id])
            ->all();
        foreach ($diagnosa_cppt as $key => $val_diag_cppt) {
            if($val_diag_cppt['a_diag_utama'] !== null){
                $is_diagnosa = true;
            }
        }
        return ['is_diagnosa_terisi'=>$is_diagnosa];
    }

    /**
    * @controller actionCetakPdfKesimpulan
    * @attribute #pasienpulang_carakeluar# => Pasien Pulang: Cara Keluar
    * @attribute #pasienpulang_kondisipulang# => Pasien Pulang: Kondisi Pulang
    * @attribute #pasienpulang_tanggalpulang# => Pasien Pulang: Tanggal Pulang
    * @attribute #pasienpulang_tanggalmeninggal# => Pasien Pulang: Tanggal Meninggal
    * @attribute #kesimpulan_kondisi# => Kesimpulan : Kondisi
    * @attribute #kesimpulan_detakjantung_hr# => Kesimpulan : Detak Jantung (HR)
    * @attribute #kesimpulan_pernafasan_rr# => Kesimpulan : Pernafasan (RR)
    * @attribute #kesimpulan_oksigen_spo2# => Kesimpulan : Oksigen (SpO2)
    * @attribute #kesimpulan_temperatur_t# => Kesimpulan : Temperatur (T)
    * @attribute #kesimpulan_gcs_eye# => Kesimpulan : GCS Eye
    * @attribute #kesimpulan_gcs_verbal# => Kesimpulan : GCS Verbal
    * @attribute #kesimpulan_gcs_motorik# => Kesimpulan : GCS Motorik
    * @attribute #kesimpulan_hasil_gcs# => Kesimpulan : Hasil GCS
    * @attribute #kesimpulan_kategori_gcs# => Kesimpulan : Kategori GCS
    * @attribute #kesimpulan_ket_kapitis# => Kesimpulan : Keterangan Kapitis
    * @attribute #kesimpulan_instruksi_lanjutan# => Kesimpulan : Instruksi Lanjutan
    * @attribute #kesimpulan_perawatan_lanjutan_tanggal# => Kesimpulan : Tanggal Perawatan Lanjutan
    * @attribute #kesimpulan_poliklinik# => Kesimpulan : Poliklinik
    * @attribute #kesimpulan_dokter_pulang# => Kesimpulan : Dokter Pulang
    * @attribute #reseptur_dokter# => Reseptur : Dokter
    * @attribute #reseptur_tanggalresep# => Reseptur : Tanggal Resep
    * @attribute #reseptur_depotujuan# => Reseptur : Depo Tujuan
    * @attribute #reseptur_iterasi# => Reseptur : Iterasi
    * @attribute #reseptur_noresep# => Reseptur : No Resep
    * @attribute #reseptur_table# => Reseptur : table reseptur
    * @attribute #tgl_cetak# => tgl_cetak
    * @attribute #nama_user# => nama_user
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara bayar
    * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter dpjp
    **/
    public function actionCetakPdfKesimpulan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $pegawai_id = $request->get('pegawai_id',0);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id',0);
        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id'=>$id_usercetak])->asArray()->one();

        if(is_null($mNamaPegawai)){
            $nama_user = $nama_usercetak;
        }else{
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }
        // try {
            $modelHeader = new InfoPasienRdV;
            $queryHeader = $modelHeader::find()
                ->andWhere([
                    'pendaftaran_id'=>$request->get('pendaftaran_id')
                ]);
            $resultHeader = $queryHeader->asArray()->one();

            // get signature path by dpjp_id
            if (!empty($resultHeader['dokter_id'])) {
                $signaturePath = Pegawai::signatureEmployee($resultHeader['dokter_id']);
            } else {
                $signaturePath = '';
            }

            $infoKesimpulan = KesimpulanRdView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();

            $infoResep = isset($infoKesimpulan['info_resep'])?$infoKesimpulan['info_resep']:null;
            $infoDetailResep = $infoKesimpulan['detail_resep'];

            $iter = '';

            if(!empty($infoDetailResep)) {
                foreach ($infoDetailResep as $key => $value) {
                    $iter = $value['iter'];
                }
            }
            
            $data_obat = $this->getInfoResepturDetail($pendaftaran_id);
            $iter = ($iter != '') ? $iter : '-';
            $datajenazah = $this->getPersetujuanJenazah($pendaftaran_id);

            $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
            $pegawailogin      = Pegawai::findOne($pegawailogin_id);
            $pegawailogin_nama = $pegawailogin->nama_pegawai;
            $waktu_cetak       = date('d-m-Y H:i:s');

            $print = new DocoPrint();
            $print->attributes = [
                '#pasienpulang_carakeluar#' => @$infoKesimpulan['carakeluar_nama'],
                '#pasienpulang_kondisipulang#' => @$infoKesimpulan['kondisikeluar_nama'],
                '#pasienpulang_tanggalpulang#' =>  $infoKesimpulan ? ($infoKesimpulan['tglpasienpulang'] ? date('d F Y H:i:s', strtotime($infoKesimpulan['tglpasienpulang'])) : '') : '',
                '#pasienpulang_tanggalmeninggal#' =>  $infoKesimpulan ? ($infoKesimpulan['tgl_meninggal'] ? date('d F Y H:i:s', strtotime($infoKesimpulan['tgl_meninggal'])) : '') : '',
                '#kesimpulan_kondisi#' => @$infoKesimpulan['kondisi'],
                '#kesimpulan_detakjantung_hr#' => @$infoKesimpulan['hr'],
                '#kesimpulan_pernafasan_rr#' => @$infoKesimpulan['rr'],
                '#kesimpulan_oksigen_spo2#' => @$infoKesimpulan['spo2'],
                '#kesimpulan_temperatur_t#' => @$infoKesimpulan['t'],
                '#kesimpulan_gcs_eye#' => @$infoKesimpulan['gcs_eye_nama'],
                '#kesimpulan_gcs_verbal#' => @$infoKesimpulan['gcs_verbal_nama'],
                '#kesimpulan_gcs_motorik#' => @$infoKesimpulan['gcs_motorik_nama'],
                '#kesimpulan_hasil_gcs#' => @$infoKesimpulan['hasil_gcs'],
                '#kesimpulan_kategori_gcs#' => @$infoKesimpulan['gcs_kategori'],
                '#kesimpulan_ket_kapitis#' => isset($infoKesimpulan['is_kapitis']) && $infoKesimpulan['is_kapitis'] == TRUE ? 'Ya':'Tidak',
                '#kesimpulan_instruksi_lanjutan#' => @$infoKesimpulan['instruksi_lanjutan'],
                '#kesimpulan_perawatan_lanjutan_tanggal#' => $infoKesimpulan ? ($infoKesimpulan['tgl_lanjut_rawat'] ? date('d F Y H:i:s', strtotime($infoKesimpulan['tgl_lanjut_rawat'])) : '') : '',
                '#kesimpulan_poliklinik#' => @$infoKesimpulan['poliklinik_nama'],
                '#kesimpulan_dokter_pulang#' => @$infoKesimpulan['dokter_pulang'],
                '#reseptur_dokter#' => @$infoResep['nama_pegawai'],
                '#reseptur_tanggalresep#' => $infoResep ? ($infoResep['tglreseptur'] ? date('d F Y H:i:s', strtotime($infoResep['tglreseptur'])) : '') : '',
                '#reseptur_depotujuan#' => @$infoResep['ruangan_tujuan'],
                '#reseptur_iterasi#' => @$iter,
                '#reseptur_noresep#' => @$infoResep['noresep'],
                '#reseptur_table#' => $this->renderPartial('table_reseptur',['data_obat'=>$data_obat]),
                '#nama_user#' => $nama_user,
                '#tgl_cetak#' => date('d F Y H:i:s'),
                '#inf_norekammedik#' => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
                '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d F Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
                '#inf_nopendaftaran#' => $resultHeader ? @$resultHeader['no_pendaftaran'] : '',
                '#inf_namapasien#' => $resultHeader ? @$resultHeader['nama_pasien'] : '',
                '#inf_jeniskelamin#' => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
                '#inf_kasuspenyakit#' => $resultHeader ? @$resultHeader['jeniskasuspenyakit_nama'] : '',
                '#inf_tgllahir#' => $resultHeader ? (isset($resultHeader['tanggal_lahir']) ? date('d F Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
                '#inf_umur#' => $resultHeader ? @$resultHeader['umur'] : '',
                '#inf_dokterjaga#' => $resultHeader ? @$resultHeader['dokter_jaga'] : '',
                '#inf_kelaspelayanan#' => $resultHeader ? @$resultHeader['kelaspelayanan_nama'] : '',
                '#inf_penjamin#' => $resultHeader ? @$resultHeader['penjamin_nama'] : '',
                '#inf_carabayar#' => $resultHeader ? @$resultHeader['carabayar_nama'] : '',
                '#inf_dokterdpjp#' => $resultHeader ? @$resultHeader['dokter'] : '',
                '#pelayanan_jenazah#' => $this->renderPartial('table_jenazah', ['data' => $datajenazah]),
                '#jenazah_kondisipasien#' => isset($datajenazah['kondisipasien']['kondisi']) ? @$datajenazah['kondisipasien']['kondisi'] : '-',
                '#jenazah_namapj#' => isset($datajenazah['kondisipasien']['penanggungjawab_nama']) ? @$datajenazah['kondisipasien']['penanggungjawab_nama'] : '-',
                '#jenazah_umur#' => isset($datajenazah['kondisipasien']['umur_pj']) ? @$datajenazah['kondisipasien']['umur_pj'] : '-',
                '#jenazah_alamat#' => isset($datajenazah['kondisipasien']['alamat']) ? @$datajenazah['kondisipasien']['alamat'] : '-',
                '#jenazah_jkpj#' => isset($datajenazah['kondisipasien']['jenis_kelamin_pj']) ? @$datajenazah['kondisipasien']['jenis_kelamin_pj'] : '-',
                '#jenazah_nokontak#' => isset($datajenazah['kondisipasien']['no_kontak']) ? @$datajenazah['kondisipasien']['no_kontak'] : '-',
                '#jenazah_hubunganpj#' => isset($datajenazah['kondisipasien']['hubungan_kel']) ? @$datajenazah['kondisipasien']['hubungan_kel'] : '-',
                '#table_tindakan#' => $this->renderPartial('table_tindakan',['tindakan'=>@$datajenazah['list_tindakan']['tindakan']]),
                '#table_obatalkes#' => $this->renderPartial('table_obat',['obat'=>@$datajenazah['list_tindakan']['obat']]),
                '#table_linen#' => $this->renderPartial('table_linen',['linen'=>@$datajenazah['list_linen']['linen']]),
                '#table_alat#' => $this->renderPartial('table_alat',['alat'=>@$datajenazah['list_linen']['alat']]),
                '#hari_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s"), 'w'),
                '#tanggal_skr#' => $this->helper->convertDate(date("Y-m-d H:i:s")),
                '#ttd_dokter#' => $signaturePath,
                '#nama_pegawai#' => $pegawailogin_nama,
                '#timestamps#' => $waktu_cetak,
            ];
            $print->Output();
        // } catch (\yii\db\Exception $e) {
        //     throw new \Exception($e->getMessage(), 1);
        // } catch (\Exception $e) {
        //     throw new \Exception($e->getMessage(), 1);
        // }
    }

    /**
    * @controller actionCetakPdfSuratKematian
    * @attribute #no_rekam_medik# => menampilkan nomer rekam medik
    * @attribute #nama_pasien# => menampilkan nama pasien
    * @attribute #no_identitas_pasien# => menampilkan no identitas pasien
    * @attribute #jenis_kelamin# => menampilkan jenis kelamin
    * @attribute #tempat_lahir# => menampilkan tempat lahir
    * @attribute #tanggal_lahir# => menampilkan tanggal lahir
    * @attribute #agama_pasien# => menampilkan agama
    * @attribute #alamat_pasien# => menampilkan alamat
    * @attribute #rt# => menampilkan rt
    * @attribute #rw# => menampilkan rw
    * @attribute #kelurahan_nama# => menampilkan kelurahan
    * @attribute #kecamatan_nama# => menampilkan keccamatan
    * @attribute #kabupaten_nama# => menampilkan kabupaten
    * @attribute #kode_pos# => menampilkan kode pos
    * @attribute #no_telepon_pasien# => menampilkan no telepon
    * @attribute #status_kependudukan# => menampilkan status kependudukan
    * @attribute #status_jenazah# => menampilkan status jenazah
    * @attribute #tgl_kremasi# => menampilkan tanggal dimakamkan atau dikremasi
    * @attribute #nama_pemeriksa_jenazah# => menampilkan nama pemeriksa jenazah
    * @attribute #kualifikasi_pemeriksa# => menampilkan kualifikasi pemeriksa
    * @attribute #waktu_pemeriksaan_jenazah# => menampilkan waktu pemeriksaan jenazah
    * @attribute #dasar_diagnosis# => menampilkan dasar diagnosis
    * @attribute #kelompok_kematian# => menampilkan kelompok kematian
    * @attribute #tgl_meninggal# => menampilkan tanggal meninggal
    * @attribute #umur# => menampilkan tanggal meninggal
    * @attribute #nokode_rumahsakit# => menampilkan kode rs
    * @attribute #nama_rumahsakit# => menampilkan nama rs
    * @attribute #bulan# => menampilkan bulan
    * @attribute #tahun# => menampilkan tahun
    * @attribute #tempat_kematian# => menampilkan tempat kematian
    * @attribute #penyebab_langsung# => menampilkan penyebab langsung
    * @attribute #penyebab_antara# => menampilkan Penyebab antara
    * @attribute #penyebab_dasar# => menampilkan penyebab dasar
    * @attribute #kondisi_lain# => menampilkan kondisi lain
    * @attribute #penyebab_utama_bayi# => menampilkan penyebab utama bayi
    * @attribute #penyebab_utama_ibu# => menampilkan penyebab utama ibu
    * @attribute #penyebab_lain_bayi# => menampilkan penyebab lain bayi
    * @attribute #penyebab_lain_ibu# => menampilkan penyebab lain ibu
    * @attribute #dokter# => menampilkan dokter
    * @attribute #jabatan_nama# => menampilkan jabatan dokter
    * @attribute #pihak_menerima# => menampilkan pihak menerima
    * @attribute #hubungan_penerima# => menampilkan hubungan penerima
    **/

    public function actionCetakPdfSuratKematian()
    {

        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $ruangan_id = $request->get('ruangan_id',0);

        $nama_user = '';
        $caraKeluarMeninggalId = (new DocoConstansId)->actionGetId('meninggal');
        $pendaftaran = Pendaftaran::find()
            ->select(['pendaftaran_id' ,'pegawai_id', 'pasien_id'])
            ->where(['pendaftaran_id'=>$pendaftaran_id])
            ->one();
        $pasien = PasienV::find()
            ->select([
                'pasien_id',
                'tanggal_lahir',
                'no_rekam_medik',
                'nama_pasien',
                'no_identitas_pasien',
                'jenis_kelamin',
                'tempat_lahir',
                'agama_pasien',
                'alamat_pasien',
                'rt',
                'rw',
                'kelurahan_nama',
                'kecamatan_nama',
                'kabupaten_nama',
                'kode_pos',
                'no_telepon_pasien',
                'warga_negara'
            ])
            ->where(['pasien_id'=>$pendaftaran['pasien_id']])
            ->asArray()
            ->one();
        $pasienPulang = PasienPulang::find()
            ->select([
                'tgl_meninggal',
                'tgl_kremasi',
                'waktu_pemeriksaan_jenazah',
                'status_jenazah',
                'nama_pemeriksa_jenazah',
                'kualifikasi_pemeriksa',
                'dasar_diagnosis',
                'kelompok_kematian',
                'tempat_kematian',
                'penyebab_langsung',
                'penyebab_antara',
                'penyebab_dasar',
                'kondisi_lain',
                'penyebab_utama_bayi',
                'penyebab_utama_ibu',
                'penyebab_lain_bayi',
                'penyebab_lain_ibu',
                'pihak_menerima',
                'hubungan_penerima',
            ])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['or', 
                ['carakeluar_id' => $caraKeluarMeninggalId], 
                ['is_meninggal' => true]
            ])
            ->asArray()
            ->one();
        $profilrs = ProfilRumahSakitM::find()->one();
        $pegawai = PegawaiView::find()->where(['pegawai_id'=>$pendaftaran['pegawai_id']])->asArray()->one();
        $bulan = date('F');
        $tahun = date('Y');
        $tanggal = date('d');
        $copyImages = Yii::$app->urlManagerFrontend->createUrl('')."media/img/background/copy_images.png";
        try {
            $print = new DocoPrint();
            $tglMeninggal = @$pasienPulang['tgl_meninggal'];
            $tglLahir = @$pasien['tanggal_lahir'];
            $tglKremasi = @$pasienPulang['tgl_kremasi'];
            $waktuPemeriksaanJenazah = @$pasienPulang['waktu_pemeriksaan_jenazah'];
            $umur = $pasien['tanggal_lahir'];
            if ($umur) {
                $umur = @DocoHelpers::getUmur($pasien['tanggal_lahir'], true);
            }
            if ($tglMeninggal) {
                $tglMeninggal = date('d/m/Y H:i:s',strtotime($tglMeninggal));
            }
            if ($tglLahir) {
                $tglLahir = date('d/m/Y',strtotime($tglLahir));
            }
            if ($tglKremasi) {
                $tglKremasi = date('d/m/Y H:i:s', strtotime($tglKremasi));
            }
            if ($waktuPemeriksaanJenazah) {
                $waktuPemeriksaanJenazah = date('d/m/Y H:i:s', strtotime($waktuPemeriksaanJenazah));
            }
            $print->attributes = [
                '#no_rekam_medik#' => @$pasien['no_rekam_medik'],
                '#nama_pasien#' => @$pasien['nama_pasien'],
                '#no_identitas_pasien#' => @$pasien['no_identitas_pasien'],
                '#jenis_kelamin#' => @$pasien['jenis_kelamin'],
                '#tempat_lahir#' => @$pasien['tempat_lahir'],
                '#tanggal_lahir#' => $tglLahir,
                '#agama_pasien#' => @$pasien['agama_pasien'],
                '#alamat_pasien#' => @$pasien['alamat_pasien'],
                '#rt#' => @$pasien['rt'],
                '#rw#' => @$pasien['rw'],
                '#kelurahan_nama#' => @$pasien['kelurahan_nama'],
                '#kecamatan_nama#' => @$pasien['kecamatan_nama'],
                '#kabupaten_nama#' => @$pasien['kabupaten_nama'],
                // '#kode_pos#' => '45231'
                // '#no_telepon_pasien#' => 08921313123,
                '#kode_pos#' => @$pasien['kode_pos'],
                '#no_telepon_pasien#' => @$pasien['no_telepon_pasien'],
                '#status_kependudukan#' => $pasien['warga_negara'] == 308 ? 'Penduduk' : 'Bukan Penduduk',
                '#status_jenazah#' => @$pasienPulang['status_jenazah'],
                '#tgl_kremasi#' => $tglKremasi,
                '#nama_pemeriksa_jenazah#' => @$pasienPulang['nama_pemeriksa_jenazah'],
                '#kualifikasi_pemeriksa#' => @$pasienPulang['kualifikasi_pemeriksa'],
                '#waktu_pemeriksaan_jenazah#' => $waktuPemeriksaanJenazah,
                '#dasar_diagnosis#' => @$pasienPulang['dasar_diagnosis'],
                '#kelompok_kematian#' => @$pasienPulang['kelompok_kematian'],
                '#tgl_meninggal#' => $tglMeninggal,
                '#umur#' => $umur,
                '#nokode_rumahsakit#' => @$profilrs['nokode_rumahsakit'],
                '#nama_rumahsakit#' => @$profilrs['nama_rumahsakit'],
                '#bulan#' => @$bulan,
                '#tahun#' => @$tahun,
                '#tanggal#' => @$tanggal,
                '#tempat_kematian#' => @$pasienPulang['tempat_kematian'],
                '#penyebab_langsung#' => @$pasienPulang['penyebab_langsung'],
                '#penyebab_antara#' => @$pasienPulang['penyebab_antara'],
                '#penyebab_dasar#' => @$pasienPulang['penyebab_dasar'],
                '#kondisi_lain#' => @$pasienPulang['kondisi_lain'],
                '#penyebab_utama_bayi#' => @$pasienPulang['penyebab_utama_bayi'],
                '#penyebab_utama_ibu#' => @$pasienPulang['penyebab_utama_ibu'],
                '#penyebab_lain_bayi#' => @$pasienPulang['penyebab_lain_bayi'],
                '#penyebab_lain_ibu#' => @$pasienPulang['penyebab_lain_ibu'],
                '#dokter#' => @$pegawai['nama'],
                '#jabatan_nama#' => @$pegawai['jabatan_nama'],
                '#pihak_menerima#' => @$pasienPulang['pihak_menerima'],
                '#hubungan_penerima#' => @$pasienPulang['hubungan_penerima'],
                '#copy_images#' => $copyImages,
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \Exception($e->getMessage(), 1);
        }
    }

        /**
    * @controller actionCetakSpri
    * @attribute #pasienpulang_carakeluar# => Pasien Pulang: Cara Keluar
    * @attribute #pasienpulang_kondisipulang# => Pasien Pulang: Kondisi Pulang
    * @attribute #pasienpulang_tanggalpulang# => Pasien Pulang: Tanggal Pulang
    * @attribute #pasienpulang_tanggalmeninggal# => Pasien Pulang: Tanggal Meninggal
    * @attribute #kesimpulan_kondisi# => Kesimpulan : Kondisi
    * @attribute #kesimpulan_detakjantung_hr# => Kesimpulan : Detak Jantung (HR)
    * @attribute #kesimpulan_pernafasan_rr# => Kesimpulan : Pernafasan (RR)
    * @attribute #kesimpulan_oksigen_spo2# => Kesimpulan : Oksigen (SpO2)
    * @attribute #kesimpulan_temperatur_t# => Kesimpulan : Temperatur (T)
    * @attribute #kesimpulan_gcs_eye# => Kesimpulan : GCS Eye
    * @attribute #kesimpulan_gcs_verbal# => Kesimpulan : GCS Verbal
    * @attribute #kesimpulan_gcs_motorik# => Kesimpulan : GCS Motorik
    * @attribute #kesimpulan_hasil_gcs# => Kesimpulan : Hasil GCS
    * @attribute #kesimpulan_kategori_gcs# => Kesimpulan : Kategori GCS
    * @attribute #kesimpulan_ket_kapitis# => Kesimpulan : Keterangan Kapitis
    * @attribute #kesimpulan_instruksi_lanjutan# => Kesimpulan : Instruksi Lanjutan
    * @attribute #kesimpulan_perawatan_lanjutan_tanggal# => Kesimpulan : Tanggal Perawatan Lanjutan
    * @attribute #kesimpulan_poliklinik# => Kesimpulan : Poliklinik
    * @attribute #kesimpulan_dokter_pulang# => Kesimpulan : Dokter Pulang
    * @attribute #reseptur_dokter# => Reseptur : Dokter
    * @attribute #reseptur_tanggalresep# => Reseptur : Tanggal Resep
    * @attribute #reseptur_depotujuan# => Reseptur : Depo Tujuan
    * @attribute #reseptur_iterasi# => Reseptur : Iterasi
    * @attribute #reseptur_noresep# => Reseptur : No Resep
    * @attribute #reseptur_table# => Reseptur : table reseptur
    * @attribute #tgl_cetak# => tgl_cetak
    * @attribute #nama_user# => nama_user
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara bayar
    * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter dpjp
    **/
    public function actionCetakSpri()
    {
        return Yii::$app->docoPlugin->execute('cetak_spri');
    }

    public function actionGetLinen()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $post = $request->post();
        try {
            $getData = Barang::find();
            if(isset($post['term'])){
                $getData->andWhere(['ILIKE', 'barang_nama', $post['term']]);
            }
            $getData->limit(30);
            return $getData->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }
    public function actionGetAlat()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $post = $request->post();
        try {
            $getData = ObatAlkes::find();
            if(isset($post['term'])){
                $getData->andWhere(['ILIKE', 'obatalkes_nama', $post['term']]);
            }
            $getData->limit(30);
            return $getData->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }
    public function actionGetObatAlkes()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $post = $request->post();
        try {
            $getData = InfoStokObatAlkesView::find();
            $getData->select(['obatalkes_nama', 'obatalkes_id', 'ruangan_id', 'obatalkes_kode', 'qty_tersedia','satuankecil_id', 'satuankecil_nama', 'hargaygdipakai as hargajual', 'harganetto_ygdipakai as harganetto', 'hn_margin as jmlmargin', 'hn_diskon as jmldiscount', 'hn_ppn as jmlppn', 'ppn as persenppn', 'disc as persendiscount', 'margin as persenmargin']);
            if(isset($post['term'])){
                $getData->andWhere(['ILIKE', 'obatalkes_nama', $post['term']]);
            }
            if(isset($post['ruangan_id'])){
                $getData->andWhere(['ruangan_id' => $post['ruangan_id']]);
            }
            $getData->andWhere(['>', 'qty_tersedia', 0]);
            $getData->limit(30);
            return $getData->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }
    public function actionGetTarifRs($ruangan_id, $kelaspelayanan_id, $penjamin_id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = InfoTarifRs::find()->where([
                    'ruangan_id' => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'penjamin_id' => $penjamin_id
            ]);
            if(isset($post['term'])){
                $model->andWhere(['ILIKE', 'daftartindakan_nama', $post['term']]);
            }
            $model->select(['daftartindakan_id', 'daftartindakan_nama', 'komponentarif_id', 'harga_tariftindakan', 'persencyto_tindakan', 'persendiskon_tindakan', 'tariftindakan_id', 'ruangan_id', 'kelaspelayanan_id', 'harga_tariftindakan as tarif_satuan', 'harga_tariftindakan as tarif_tindakan', '((harga_tariftindakan * persencyto_tindakan) / 100) as tarifcyto_tindakan']);
            $model->orderBy(['daftartindakan_nama' => SORT_ASC]);
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
    protected function orderPelayananJenazah($pendaftaran_id,$form_attribute)
    {
        try{
            $restJenazah = Yii::$app->docoRest->jenazah;
            $request = $restJenazah->post('order/create', [
                'query'=>['id' => $pendaftaran_id],
                'form_params'=>$form_attribute
            ]);

            $response = json_decode($request->getBody(),true);
            return $response['response'];
        }catch(RequestException $e){
            return false;
        } catch(\Exception $e){
            return false;
        }
    }
    public function getPersetujuanJenazah($pendaftaran_id)
    {
        $result = [];
        try {
            $getData = InfoPasienMeninggal::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if(count($getData) == 0){
                return [];
            }
            $result = [
                'kondisipasien' => [],
                'list_tindakan' => [
                    'tindakan' => [],
                    'obat' => []
                ],
                'list_linen' => [
                    'linen' => [],
                    'alat' => [],
                ]
            ];
            $getLinen = InfoPasienMeninggalDetail::find()->select(['pendaftaran_id', 'alat_id', 'nama_alat', 'qty'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();
            $linen = $alat = $tindakan = $obat = [];
            if(count($getLinen) > 0){
                foreach ($getLinen as $key => $value) {
                    if($value['alat_id'] == 'LINEN'){
                        $linen[] = $value;
                    }else if($value['alat_id'] == 'ALAT'){
                        $alat[] = $value;
                    }
                }
            }
            $getOrder = InfoPasienMeninggalDetail2::find()->select(['pendaftaran_id', 'tindakan_obat', 'qty', 'satuan', 'tarif_satuan', 'jenis', '(tarif_satuan * qty) as total_tarif'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();
            if(count($getOrder) > 0){
                foreach ($getOrder as $key => $value) {
                    if($value['jenis'] == 'tindakan'){
                        $tindakan[] = $value;
                    }else if($value['jenis'] == 'obat'){
                        $obat[] = $value;
                    }
                }
            }
            $result['kondisipasien'] = $getData;
            $result['list_linen']['linen'] = $linen;
            $result['list_linen']['alat'] = $alat;
            $result['list_tindakan']['tindakan'] = $tindakan;
            $result['list_tindakan']['obat'] = $obat;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getInfoResepturDetail($pendaftaran_id){
        $result = InfoResepturDetailView::find()
        ->select([
            'obatalkes_nama',
            'satuan_kecil',
            'signa_nama',
            'qty_reseptur',
            'nama_rute'
        ])
        ->where([
            'pendaftaran_id' => $pendaftaran_id,
            // 'is_bayar' => true,
            'status_reseptur_id' => DocoConstants::RESEPTUR_SUDAH_DIPROSES,
        ])
        ->orderBy(['racikan_nama' => SORT_ASC])
        ->asArray()
        ->all();
        return $result;
    }
}
