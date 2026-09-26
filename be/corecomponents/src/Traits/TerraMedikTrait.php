<?php

namespace Doco\Traits;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\models\RiwayatSoapTerra;
use Doco\models\RiwayatResepTerra;
use Doco\models\RiwayatLabTerra;
use Doco\models\RiwayatRadiologiTerra;
use Doco\models\RiwayatBedahTerra;
use Doco\models\RiwayatMcuTerra;
use Doco\models\RiwayatFisioterapiTerra;
use Doco\models\RiwayatBblTerra;
use Doco\models\RiwayatResumemedisTerra;
use Doco\models\RiwayatKunjunganR;
use Doco\models\Pegawai;

/**
 * Trait of Nursing Note
 */
trait TerraMedikTrait
{
    /**
     * This function will return API datatable
     *
     * @return Json
     * @author : Ilhamsyah
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

     public function actionHistorySoapTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_cppt = RiwayatSoapTerra::find()
             ->where(['pasien_id' => $pasien_id])
             ->orderBy(['tgl_soap' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
             $data_cppt = $data_cppt->andWhere(['dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $data_cppt = $data_cppt->andWhere(['between', 'tgl_soap', $startDate, $endDate]);
             }
         }

         $data_cppt = $data_cppt->orderBy(['tgl_soap' => SORT_DESC, 'riwayatsoap_id' => SORT_DESC]);
         $total = $data_cppt->count();

         $data_cppt = $data_cppt->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_cppt = $data_cppt->asArray()->all();

         return [
             'data'  => $data_cppt,
             'total' => $total,
         ];
     }

     public function actionHistoryResepTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_resep = RiwayatResepTerra::find()
             ->select(['id', 'kode_trans', 'kode_trans_detil', 'no_resep', 'tgl_transaksi', 'no_rm', 'riwayat_resep.nama_dokter', 'nama_barang', 'satuan', 'jumlah', 'concat(signa,\' \',signa_tambahan) as signa', 'pendaftaran_t.dokter_id'])
             ->leftJoin('riwayatkunjungan_r pendaftaran_t', 'history.riwayat_resep.regis_id = pendaftaran_t.pendaftaranold_id::text')
             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['tgl_transaksi' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_resep = $data_resep->andWhere(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $data_resep = $data_resep->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $data_resep = $data_resep->andWhere(['between', 'tgl_transaksi', $startDate, $endDate]);
             }
         }

         $total = $data_resep->count();

         $data_resep = $data_resep->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_resep = $data_resep->asArray()->all();

         return [
             'data'  => $data_resep,
             'total' => $total,
         ];
     }

     public function actionHistoryLabTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_lab = RiwayatLabTerra::find()
             ->select(['id', 'order_id', 'finish_note', 'order_date', 'no_rm', 'doctor_periksa', 'lpgrname as group_pemeriksaan', 'lpname as nama_pemeriksaan', 'result', 'nilai_normal', 'lpsatuan as satuan_pemeriksaan', 'pendaftaran_t.dokter_id'])
             ->leftJoin('riwayatkunjungan_r pendaftaran_t', 'history.riwayat_lab.regis_id = pendaftaran_t.pendaftaranold_id::text')
             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['order_date' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
             $data_lab = $data_lab->andWhere(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $data_lab = $data_lab->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $data_lab = $data_lab->andWhere(['between', 'order_date', $startDate, $endDate]);
             }
         }

         $total = $data_lab->count();

         $data_lab = $data_lab->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_lab = $data_lab->asArray()->all();

         return [
             'data'  => $data_lab,
             'total' => $total,
         ];
     }

     public function actionHistoryRadiologiTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_radiologi = RiwayatRadiologiTerra::find()
             ->select(['id', 'order_id', 'tgl_order', 'tgl_selesai', 'no_rm', 'doctor_rad', new \yii\db\Expression("COALESCE(pendaftaran_t.nama_dokter, pendaftaran_t.dokter_internal_perujuk) AS doctor_perujuk"), 'jenis_periksa', 'riwayat_rad.diagnosa', 'diag_klinik',  'pendaftaran_t.dokter_id'])
             ->leftJoin('riwayatkunjungan_r pendaftaran_t', 'history.riwayat_rad.regis_id = pendaftaran_t.pendaftaranold_id::text')
             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['tgl_order' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
             $data_radiologi = $data_radiologi->andWhere(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $data_radiologi = $data_radiologi->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $data_radiologi = $data_radiologi->andWhere(['between', 'tgl_order', $startDate, $endDate]);

             }
         }

         $total = $data_radiologi->count();

         $data_radiologi = $data_radiologi->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_radiologi = $data_radiologi->asArray()->all();

         return [
             'data'  => $data_radiologi,
             'total' => $total,
         ];
     }

     public function actionHistoryBedahTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_bedah = RiwayatBedahTerra::find()
             ->select([
                      'id', 'order_id', 'regis_id', 'COALESCE(pendaftaran_t.dokter_internal_perujuk, history.riwayat_operasi.dokter_operator) AS dokter_operator', 'dokter_anastesi', 'asisten_operator',
                      'asisten_anastesi', 'tindakan_name', 'jenis_pembedahan', 'tgl_tindakan', 'tgl_selesai',
                      'log_operasi', 'post_operasi', 'jaringan_incisi', 'luas_operasi', 'pemeriksaan_patologi_anatomi',
                      'hasil'
                  ])
              ->leftJoin('riwayatkunjungan_r as pendaftaran_t', 'history.riwayat_operasi.regis_id = pendaftaran_t.pendaftaranold_id::text')

             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['tgl_tindakan' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_bedah = $data_bedah->where(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $data_bedah = $data_bedah->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $data_bedah = $data_bedah->andWhere(['between', 'tgl_tindakan', $startDate, $endDate]);
             }
         }

         $total = $data_bedah->count();

         $data_bedah = $data_bedah->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_bedah = $data_bedah->asArray()->all();

         return [
             'data'  => $data_bedah,
             'total' => $total,
         ];
     }

     public function actionHistoryMcuTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $data_mcu = RiwayatMcuTerra::find()
             ->select([
                      'id', 'mcu_paket_id', 'regis_id', 'paket_name', 'module_name', 'data',
                      'COALESCE(pendaftaran_t.dokter_internal_perujuk, history.riwayat_mcu.koordinator_tim_dokter) AS koordinator_tim_dokter', 'pendaftaran_t.dokter_id',
                  ])
              ->leftJoin('riwayatkunjungan_r as pendaftaran_t', 'history.riwayat_mcu.regis_id = pendaftaran_t.pendaftaranold_id::text')

             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['regdate' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_mcu = $data_mcu->where(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $data_mcu = $data_mcu->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         $total = $data_mcu->count();

         $data_mcu = $data_mcu->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data_mcu = $data_mcu->asArray()->all();

         return [
             'data'  => $data_mcu,
             'total' => $total,
         ];
     }

     public function actionHistoryFisioterapiTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $queryFisioterapi = RiwayatFisioterapiTerra::find()
            ->select([
                'history.riwayat_fisio.id',
                'history.riwayat_fisio.regdate',
                'history.riwayat_fisio.no_rm',
                'history.riwayat_fisio.order_id',
                'history.riwayat_fisio.regis_id',
                'history.riwayat_fisio.nama_pasien',
                'history.riwayat_fisio.tgl_order',
                'history.riwayat_fisio.tgl_selesai',
                'history.riwayat_fisio.dept',
                'history.riwayat_fisio.diagnosa',
                'COALESCE(pendaftaran_t.nama_dokter, history.riwayat_fisio.referrer_internal_doctor) AS referrer_internal_doctor',
                'pendaftaran_t.dokter_id',
                'pendaftaran_t.pendaftaranold_id as no_pendaftaran',
            ])
            ->leftJoin('riwayatkunjungan_r pendaftaran_t', 'history.riwayat_fisio.regis_id::int = pendaftaran_t.pendaftaranold_id')
            ->where(['pendaftaran_t.pasien_id' => $pasien_id])
            ->orderBy(['tgl_order' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
             $queryFisioterapi = $queryFisioterapi->andWhere(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $queryFisioterapi = $queryFisioterapi->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $queryFisioterapi = $queryFisioterapi->andWhere(['between', 'tgl_order', $startDate, $endDate]);
             }
         }

         $total = $queryFisioterapi->count();

         $queryFisioterapi = $queryFisioterapi->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data = $queryFisioterapi->asArray()->all();

         return [
             'data'  => $data,
             'total' => $total,
         ];
     }

     public function actionHistoryBblTerraMedik()
     {
         $params       = Yii::$app->request;
         $pasien_id = $params->get('pasien_id', 0);
         $is_dokter = $params->get('is_dokter', false);
         $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

         $queryBbl = RiwayatBblTerra::find()
             ->select(['id', 'nama_bayi', 'hari_lahir', 'date_birth_bayi', 'hari_lahir', 'jam_lahir', 'menit_lahir', 'nama_bayi', 'panjang', 'berat', 'COALESCE(pendaftaran_t.nama_dokter, history.riwayat_bbl.doctor) AS doctor'])
             ->leftJoin('riwayatkunjungan_r pendaftaran_t', 'history.riwayat_bbl.regis_ibu_id::int = pendaftaran_t.pendaftaranold_id')
             ->where(['pendaftaran_t.pasien_id' => $pasien_id])
             ->orderBy(['regdate' => SORT_DESC]);

         if (isset($_GET['advanced-filter']['dokter_id'])) {
             $queryBbl = $queryBbl->andWhere(['pendaftaran_t.dokter_id' => $_GET['advanced-filter']['dokter_id']]);
         }else{
            if($is_dokter){
                $queryBbl = $queryBbl->andWhere(['pendaftaran_t.dokter_id' => $pegawai_id]);
             }
         }

         if(isset($_GET['advanced-filter']['tanggal'])){
             $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
             if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                 $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $rangeDate[0])));
                 $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $rangeDate[1])));
                 $startDate = "$startDate 00:00:00";
                 $endDate = "$endDate 23:59:59";
                 $queryBbl = $queryBbl
                    ->andWhere(['>=','date_birth_bayi', $startDate])
                    ->andWhere(['<=','date_birth_bayi', $endDate]);
             }
         }

         $total = $queryBbl->count();

         $queryBbl = $queryBbl->limit($_GET['per-page'])
             ->offset(($_GET['page'] - 1) * $_GET['per-page']);
         $data = $queryBbl->asArray()->all();

        return [
            'data'  => $data,
            'total' => $total,
        ];
     }

         /**
     * Get Data History Resume Medis Terra Medik
     */
    public function actionHistoryResumemedisTerraMedik()
    {
        $model = new RiwayatResumemedisTerra;
        $pegawai_id   = Yii::$app->jwt->user->pegawai_id;
        $query = $model::find()
            ->select([
                'id',
                'riwayatkunjungan_r.pasien_id',
                'regis_id',
                'regdate',
                'riwayatkunjungan_r.dokter_id as pegawai_id',
                'COALESCE(riwayatkunjungan_r.dokter_internal_perujuk, history.riwayat_resumemedis_ri.dpjp) AS dpjp',
                "COALESCE(NULLIF(diagnosa_awal,''),'-') AS diagnosa_awal",
                "COALESCE(NULLIF(diagnosa_utama,''),'-') AS diagnosa_utama",
                "COALESCE(NULLIF(diagnosa_sekunder,''),'-') AS diagnosa_sekunder",
                'anamnesa',
                "COALESCE(NULLIF(alergi,''),'-') AS alergi",
                'pemeriksaan_fisik',
                "COALESCE(NULLIF(riwayat_penyakit,''),'-') AS riwayat_penyakit",
                'indikasi_pasien_dirawat',
                "COALESCE(NULLIF(lab,''),'-') AS lab",
                "COALESCE(NULLIF(radiologi,''),'-') AS radiologi",
                "COALESCE(NULLIF(lainlain,''),'-') AS lainlain",
                "COALESCE(NULLIF(terapi_dan_tindakan_medis,''),'-') AS terapi_dan_tindakan_medis",
                "COALESCE(NULLIF(konsultasi,''),'-') AS konsultasi",
                'obat_selama_di_rs',
                'obat_dibawa_pulang',
                'kondisi_keluar',
                "COALESCE(NULLIF(tindak_lanjut,''),'-') AS tindak_lanjut"
            ])->leftJoin('riwayatkunjungan_r', 'history.riwayat_resumemedis_ri.regis_id::int = riwayatkunjungan_r.pendaftaranold_id');

        $request = Yii::$app->request;
        $is_dokter = $request->get('is_dokter', false);
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $advancedFilters = $request->get('advanced-filter', []);
        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tanggal'])) {
                $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
                if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                    $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                    $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                    $query->andWhere(['between', 'regdate', $startDate, $endDate]);
                }
            }

            if (isset($advancedFilters['pasien_id'])) {
                $pasien_id = $advancedFilters['pasien_id'];
                $query->andWhere(['riwayatkunjungan_r.pasien_id' => $pasien_id]);
            }

            if (isset($advancedFilters['dokter_id'])) {
                $pegawai_id = $advancedFilters['dokter_id'];
                $query->andWhere(['riwayatkunjungan_r.dokter_id' => $pegawai_id]);
            }
            else{
                if($is_dokter){
                    $query = $query->andWhere(['riwayatkunjungan_r.dokter_id' => $pegawai_id]);
                 }
             }
        }



        $query->orderBy(['regdate' => SORT_DESC]);
        $total = $query->count();

        $query->limit($_GET['per-page'])
            ->offset(($_GET['page'] - 1) * $_GET['per-page']);
        $data = $query->asArray()->all();

        return [
            'data'  => $data,
            'total' => $total,
        ];
    }

    public function actionHistoryKunjunganTerraMedik()
    {
        $params       = Yii::$app->request;
        $pasien_id = $params->get('pasien_id', 0);
        $is_dokter = $params->get('is_dokter', false);
        $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

        $data_kunjungan = RiwayatKunjunganR::find()
            ->select(['riwayatkunjungan_id', 'pendaftaranold_id', 'tipe_pendaftaran', 'instalasi_nama', 'kamar', 'no_tempat_tidur', 'tgl_pendaftaran', 'dokter_id', 'COALESCE(nama_dokter, dokter_internal_perujuk) AS dokter_nama', 'diagnosa_perujuk', 'catatan_perujuk'])
            ->where(['pasien_id' => $pasien_id])
            ->orderBy(['tgl_pendaftaran' => SORT_DESC]);

        if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_kunjungan = $data_kunjungan->andWhere(['dokter_id' => $_GET['advanced-filter']['dokter_id']]);
        }else{
           if($is_dokter){
               $data_kunjungan = $data_kunjungan->andWhere(['dokter_id' => $pegawai_id]);
            }
        }

        if(isset($_GET['advanced-filter']['tanggal'])){
            $rangeDate = explode('-', $_GET['advanced-filter']['tanggal']);
            if(!empty($rangeDate[0]) && !empty($rangeDate[1])){
                $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $rangeDate[0])));
                $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $rangeDate[1])));
                $data_kunjungan = $data_kunjungan->andWhere(['between', 'tgl_pendaftaran', $startDate, $endDate]);
            }
        }

        $total = $data_kunjungan->count();

        $data_kunjungan = $data_kunjungan->limit($_GET['per-page'])
            ->offset(($_GET['page'] - 1) * $_GET['per-page']);
        $data_kunjungan = $data_kunjungan->asArray()->all();

        return [
            'data'  => $data_kunjungan,
            'total' => $total,
        ];
    }

}
