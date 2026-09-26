<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-01 14:53:21
 * @Description: setting variabel konstanta / variabel caching
 */

namespace Doco\components;

use Yii;

class DocoConstants
{
    const VAR_CRON_KUNJUNGAN = 1;
    const VAR_CRON_DIAGNOSA = 2;
    const VAR_CRON_TAGIHAN = 3;
    const VAR_CRON_LAYANAN = 4;
    const VAR_CRON_PEGAWAI = 5;
    const VAR_CRON_BAGIAN = 6;
    const VAR_CRON_NOTA = 7;
    const VAR_CRON_POTONGAN = 8;
    const VAR_CRON_ADJH = 9;
    const VAR_CRON_ADJD = 10;
    const VAR_CRON_KUNJUNGAN_RJ = 11;
    const VAR_CRON_DIAGNOSA_RJ = 12;
    const VAR_CRON_TAGIHAN_RJ = 13;

    public static $look_hari = [
        1 => 75,
        2 => 76,
        3 => 77,
        4 => 78,
        5 => 79,
        6 => 80,
        7 => 81,
    ];
    const DURATION = 24 * 60 * 60;
    // jenis diagnosa kasus via lookup
    const DIAGNOSA_KASUS_BARU = '362';
    const DIAGNOSA_KASUS_LAMA = '363';

    // variable cache diagnosa all
    const DIAGNOSA_ALL = [];

    static $status_lab = [
        473 => 'Periksa',
        474 => 'Ambil Sampel',
        475 => 'Selesai',
        476 => 'Batal',
        477 => 'Belum Periksa',
    ];

    /**
     *
     * @var description : variable cache data via satuankonversi_m
     * @return array
     * @example data <id-satuan-besar> [
     * ..
     * <id-satuan-kecil> => <nilai-konversi>
     * ]
     * @see BE: apotek/v1/allow/set-cache-konvert-satuan
     *
     */
    const KONV_SATUAN = 'konversi_satuan';
    const SATUAN_BUAH = 58;

    // status periksa
    const STATUS_PERIKSA_ANTRIAN_POLI = '1';
    const STATUS_PERIKSA_SUDAH_DIPERIKSA = '3';
    const STATUS_PERIKSA_PLG = '4';
    const STATUS_PERIKSA_AN_PENDFTRN = '339';
    const STATUS_PERIKSA_BTL_PRKS = '402';
    const STATUS_PERIKSA_BTL_KNSL = '411';
    const STATUS_PERIKSA_AN_KSR = '430';
    const STATUS_PERIKSA_RJK_RNP = '433';
    const STATUS_PERIKSA_BTL_KUNJ = '628';
    const STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP = '1058';

    // kelompok pegawai
    const KELOMPOK_PEGAWAI_DOKTER = 1;
    const KELOMPOK_PEGAWAI_PERAWAT = 2;
    const KELOMPOK_PEGAWAI_BIDAN = 3;
    const KELOMPOK_PEGAWAI_KEFARMASIAN = 4;
    const KELOMPOK_PEGAWAI_KESEHATAN = 5;
    const KELOMPOK_PEGAWAI_KESLING = 6;
    const KELOMPOK_PEGAWAI_NONKESEHATAN = 7;
    const KELOMPOK_PEGAWAI_KETERAPIAN = 8;
    const KELOMPOK_PEGAWAI_TEKMEDIK = 9;
    const KELOMPOK_PEGAWAI_TEKBIOMEDIK = 10;
    const KELOMPOK_PEGAWAI_KESTRADISIONAL = 11;
    const KELOMPOK_PEGAWAI_GIZI = 12;


    // fungsi_antrian
    const LOOKUP_TYPE_FUNGSI_ANTRIAN = 'fungsi_antrian';
    const LOOKUP_NAME_DEFAULT_PENDAFTARAN = 'Default Pendaftaran';

    // penjualan resep
    const PENJUALAN_RESEP_BEBAS = '343';
    const PENJUALAN_RESEP_RS = '344';
    const PENJUALAN_RESEP_KARYAWAN = '345';

    // jenis obat
    const JENIS_OBATALKES_OBAT = '1';
    const JENIS_OBATALKES_ALKES = '2';
    const JENIS_OBATALKES_GASMEDIS = '3';
    const JENIS_OBATALKES_DENTAL = '4';
    const JENIS_OBATALKES_XRAY = '5';
    const JENIS_OBATALKES_LABORATORIUM = '6';

    // jenis penjualan resep
    const JENIS_PENJUALAN_RS = '344';
    const JENIS_PENJUALAN_BEBAS = '343';
    const JENIS_PENJUALAN_KARYAWAN = '345';

    const LOOKUP_BPJS = 'bpjs';
    const SINGKATAN_BPJS = 'BPJS';
    const PELAYANAN_BPJS_RANAP = 1;

    // antrian
    const LIMIT_ANTRIAN = 'limit_antrian';
    const ANTRIAN_STATUS_BELUM_PANGGIL = 0;
    const ANTRIAN_STATUS_PANGGIL = 1;
    const ANTRIAN_STATUS_PILIH = 2;
    const ANTRIAN_STATUS_LEWATI = 3;
    const ANTRIAN_STATUS_BATAL = 4;

    // kelompok pegawai
    const KELOMPOK_PEGAWAI_TENAGAMEDIS = 't_medis';
    const KELOMPOK_PEGAWAI_TENAGAKEPERAWATAN = 't_keperawatan';

    // INSTALASI
    const INSTALASI_RAWAT_JALAN = "RJ";
    const INSTALASI_RAWAT_INAP = "RI";
    const INSTALASI_RAWAT_DARURAT = "RD";
    const INSTALASI_RAWAT_JALAN_DARURAT = "RJ-RD";

    // TARIF
    const KOMPONEN_TARIF = 6;

    /**
     *
     * @var description : variable cache data via lookup_m with lookup_type conditions
     * @return multidimensional array: contains all columns
     * @example ['lookup_type_1' => ['records_1', 'records_2'], 'lookup_type_2' => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getLookupByType()
     *
     */
    const VAR_CACHE_LOOKUP_BY_TYPE = 'var_cache_lookup_by_type';

    /**
     *
     * @var description : variable cache data via ruanganpegawai_mp with ruangan_id conditions
     * @return multidimensional array: contains all columns
     * @example ['ruangan_id1' => ['records_1', 'records_2'], 'ruangan_id2' => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getPegawaiRuangan()
     *
     */
    const VAR_CACHE_PEGAWAIRUANGAN = 'var_cache_pegawairuangan';

    /**
     *
     * @var description : variable cache data via penjamin_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getPenjamin()
     *
     */
    const VAR_CACHE_PENJAMIN = 'var_cache_penjamin';

    /**
     *
     * @var description : variable cache data via jabatan_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getJabatan()
     *
     */
    const VAR_CACHE_JABATAN = 'var_cache_jabatan';

    /**
     *
     * @var description : variable cache data via diagnosa_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getDiagnosa()
     *
     */
    const VAR_CACHE_DIAGNOSA = 'var_cache_diagnosa';

    /**
     *
     * @var description : variable cache data via diagnosa_m with diagnosa_imunisasi conditions true
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getDiagnosa()
     *
     */
    const VAR_CACHE_DIAGNOSA_IMUNISASI = 'var_cache_diagnosa_imunisasi';

    /**
     *
     * @var description : variable cache data via diagnosa_m with diagnosa_imunisasi conditions true
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || private function getDiagnosa()
     *
     */
    const VAR_CACHE_DIAGNOSA_KLASIFIKASI = 'var_cache_diagnosa_klasifikasi';
    const VAR_CACHE_DIAGNOSA_RUANGAN = 'var_cache_diagnosa_ruangan';
    const VAR_CACHE_KELOMPOKDIAGNOSA = 'var_cache_kelompokdiagnosa';
    const VAR_CACHE_JADWALPOLI = 'var_cache_jadwalpoli';
    const VAR_CACHE_RUJUKANKELUAR = 'var_cache_rujukankeluar';
    const VAR_CACHE_STOKOBATALKES_RUANGAN = 'var_cache_stokobatalkes_ruangan';
    const VAR_CACHE_OBATALKES = 'var_cache_obatalkes';
    const VAR_CACHE_TINDAKAN_RUANGAN = 'var_cache_tindakan_ruangan';
    const VAR_CACHE_DOKTER_RUANGAN = 'var_cache_dokter_ruangan';
    const VAR_CACHE_PERAWAT_RUANGAN = 'var_cache_perawat_ruangan';
    const VAR_CACHE_PERAWAT_RUANGAN_RI = 'var_cache_perawat_ruangan_ranap';

    /**
     *
     * @var description : except module aktif not assign to user
     * @return array
     *
     */
    const VAR_EXCEPT_MODUL = [25, 16]; // modul dcms,master


    /**
     * description : nama variable cache untuk lookup
     * @return array lookup_m
     */
    const VAR_CACHE_LOOKUP = 'var_cache_lookup';
    const VAR_CACHE_LOOKUP_ALL = 'var_cache_lookup_all';
    const VAR_CACHE_MASTER = 'var_cache_master';
    const VAR_CACHE_LOOKUP_PERAWAT = 'var_cache_lookup_keperawatan';
    const VAR_CACHE_LOKET_PENDAFTARAN = 'var_cache_loket_pendaftaran';

    /**
     * description : nama variable cache untuk pengguna
     * @return array pengguna misal
     * '1' => [
     *  'loket_id' => 1,
     *
     * ]
     */
    const VAR_CACHE_LOKET_PENGGUNA = 'var_cache_loket_pengguna';


    /**
     * @todo generate cache konfig farmasi
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    const VAR_CACHE_KONFIG_FARMASI = 'var_cache_konfig_farmasi';

    // status pemesanan
    const STATUS_PESAN_DIKIRIM = 399;
    const STATUS_PESAN_BELUM_DIKIRIM = 398;
    const STATUS_PESAN_DITERIMA = 397;

    // status pengiriman mutasi
    const STATUS_MUTASI_DIKIRIM = 401;
    const STATUS_MUTASI_DITERIMA = 400;

    // metode pengeluaran obat / barang
    const FEFO = 'FEFO';
    const FIFO = 'FIFO';
    const LIFO = 'LIFO';

    // konfig farmasi
    const HARGA_MAX = 'MAX';
    const HARGA_MIN = 'MIN';
    const HARGA_AVERAGE = 'AVERAGE';
    const HARGA_LAST = 'LAST';
    const HARGA_FIX_RATE = 'FIX RATE';

    // nilai uang
    const LT_N_UANG = 'nilai_uang'; // lookup type nilai_uang
    const VC_N_UANG = 'var_cache_nilai_uang';

    /**
     *
     * @var GCS_LIST_EYE, GCS_LIST_VERBAL, GCS_LIST_MOTORIK
     * @var description : konstanta untuk list data gcs, eye, verbal, dan motorik
     * @var .. pada kolom metodegcs_singkatan -> metodegcs_m
     *
     */
    const GCS_LIST_EYE = 'E';
    const GCS_LIST_VERBAL = 'V';
    const GCS_LIST_MOTORIK = 'M';

    /**
     *
     * @var RI, RD, LAB, RAD
     * @var description : konstanta untuk list data rawat inap, rawat darurat, lab dan radiologi
     *
     */
    const RI = 'Rawat Inap';
    const RD = 'Rawat Darurat';
    const LAB = 'Laboratorium';
    const RAD = 'Radiologi';
    const GUD = 'Gudang Farmasi';
    const REHAB = 'Rehabilitasi Medik';
    const RM = 'Rekam Medik';
    const KASIR = 'Kasir';
    const INFO = 'Informasi';
    const PDF = 'Pendaftaran & Penjadwalan';
    const BDH = 'Bedah Sentral';
    const AMB = 'Ambulan';

    /**
     *
     * @var description : variable cache data via gcs_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_GCS_MASTER = 'var_cache_gcs_master';

    /**
     *
     * @var description : variable cache data via metodegcs_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_GCS_METODE = 'var_cache_gcs_metode';

    /**
     *
     * @var description : variable cache data via bodymassindex_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_BMI = 'var_cache_bmi';
    const VAR_CACHE_BMI_ANAK = 'var_cache_bmi_anak';

    /**
     *
     * @var description : variable cache data via klasifikasitekanandarah_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_KLASIFIKASI_TEKANANDARAH = 'var_cache_klasifikasi_tekanandarah';

    /**
     *
     * @var description : variable cache data via bagiantubuh_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_BAGIANTUBUH = 'var_cache_bagiantubuh';

    /**
     *
     * @var description : variable constant flagging jenis diagnosa pasienmorbiditas_m
     * 0 = diagnosa utama
     * 1 = diagnosa masuk
     *
     */
    const VAR_PM_DU = 0;
    const VAR_PM_DM = 1;

    // buat pendaftaran IGD
    const VAR_ST = '424'; //satuan tindakan pendaftaran igd

    public function konfigFarmasi()
    {
        $connection = Yii::$app->db;
        $cache = Yii::$app->cache;
        $sql = "SELECT * FROM konfigfarmasi_k WHERE is_active = true";
        $data = $connection->createCommand($sql)->queryOne();
        $cek_cache = $cache->get('CKonfigFarmasi');
        if ($cek_cache === false) {
            $cache->set('CKonfigFarmasi', $data);
        }
        $konfig_farmasi = $cache->get('CKonfigFarmasi');
        return $konfig_farmasi;
    }

    /**
     * @todo generate cache cara bayar diulang
     * @author Arief Saputra <arief.indra@docotel.com>
     */
    const VAR_CACHE_CARA_BAYAR = 'var_cache_cara_bayar';

    /**
     * @todo generate cache group cara bayar
     * @author Ardi
     */
    const VAR_CACHE_GROUP_CARA_BAYAR = 'var_cache_group_cara_bayar';
    const VAR_CACHE_PEGAWAI_DOKTER = 'var_cache_pegawai_dokter_ruangan';

    /**
     * @todo generate cache klasifikasi pasien
     * @author Ardi
     */
    const VAR_CACHE_KLASIFIKASI_PASIEN = 'var_cache_klasifikasi_pasien';

    /**
     *
     * @var description : variable lookup type status_bayar
     *
     */
    const VAR_LU_SB = 'status_bayar';
    const STATUS_BAYAR_NULL = 9999;

    /**
     *
     * @var description : variable instalasi singkatan apotek
     *
     */
    const VAR_I_A = 'APT';

    /**
     *
     * @var description : variable instalasi singkatan rawat inap
     *
     */
    const VAR_I_RI = 'RI';

    /**
     *
     * @var description : variable cache data via ruangan_m join instalasi_m
     * @return multidimensional array: contains all columns
     * @example ['instalasi_singkatan1' => ['records_1', 'records_2'], 'instalasi_singkatan2' => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function getRuanganInstalasi()
     *
     */
    const VC_R_I = 'var_cache_ruangan_instalasi';

    /**
     *
     * @var description : variable jenis antrian poli base on lookup
     *
     */
    const VAR_JA_PEN = 179; // Penunjang
    const VAR_JA_PD = 177; // pendaftaran
    const VAR_JA_KSR = 178; // kasir
    const VAR_JA_P = 312; // poli
    const VAR_JA_F = 176; // farmasi

    /**
     *
     * @var description : variable status antrian batal
     *
     */
    const VAR_SA_B = 4;


    /**
     *
     * @var description : variable cache data via signaobat_m
     * @return multidimensional array: contains all columns
     * @example ['0' => ['records_1', 'records_2'], '1' => ['records_14']]
     * @see BE: rajal/modules/v1/controllers/AllowController/ || public function getRuanganInstalasi()
     *
     */
    const VC_SO = 'var_cache_signa_obat';
    /**
     *
     * @var description : variable jenis antrian farmasi lookup_id
     * seharusnya diganti, tapi lookup_kode nya kosong
     *
     */


    /**
     *
     * @var description : variable fungsi antrian apotek farmasi base on konfigantrianfarmasi_v
     * @var VAR_FA_ACB : antrian cara bayar
     * @var VAR_FA_NR : antrian non racikan
     * @var VAR_FA_R : antrian racikan
     * @var VAR_FA_A : antrian default apotek
     *
     */
    const VAR_FA_ACB = 326;
    const VAR_FA_NR = 325;
    const VAR_FA_R = 324;
    const VAR_FA_A = 323;

    /**
     *
     * @var description : variable lookup_type kondisi barang base on lookup_m
     * @var VAR_LU_KB : kondisi barang lookup type
     *
     */
    const VAR_LU_KB = 'kondisi_barang';

    /**
     *
     * @var description : variable lookup_type jenis SO base on lookup_m
     * @var VAR_LU_JSO : jenis stokopname lookup type
     *
     */
    const VAR_LU_JSO = 'jenis_stokopname';

    /**
     * @todo constant lookup for farmacy config
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    const VAR_MH = 'metode_harga'; // metode harga
    const VAR_OA = 'obat_antrian'; // obat antrian

    /**
     * @todo menampilkan semua attributes cache untuk cara bayar sesuai di tabel
     * @see Service Kasir|Master app\modules\v1\cache\Cache::getCaraBayar()
     * @author yaya
     *
     **/
    const CACHE_CB = 'cache_cara_bayar';

    /**
     * @todo menampilkan semua attributes cache untuk cara bayar sesuai di tabel
     * @see Service Master app\modules\v1\cache\Cache::getGroupCaraBayar()
     * @author yaya
     *
     **/
    const CACHE_GROUP_CB = 'cache_group_cara_bayar';

    /**
     * @todo menampilkan semua attributes cache untuk penjamin sesuai di tabel
     * @see Service Kasir|Master app\modules\v1\cache\Cache::getPenjamin()
     * @author yaya
     **/
    const CACHE_PENJAMIN = 'cache_penjamin';

     /**
    * @todo menampilkan semua attributes cache untuk penjamin sesuai di tabel
    * @see Service Kasir|Master app\modules\v1\cache\Cache::getPenjamin()
    * @author yaya
    **/
    const CACHE_PENJAMINCB = 'cache_penjamin_carabayar';

    /**
     * @todo menampilkan semua attributes cache untuk jenis antrian sesuai di tabel
     * @see Service Kasir|Master app\modules\v1\cache\Cache::getJenisAntrian()
     * @author yaya
     **/
    const CACHE_JENIS_ANTRIAN = 'cache_jenis_antrian';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Master app\modules\v1\cache\Cache::getFungsiAntrian()
     * @author yaya
     **/
    const CACHE_FUNGSI_ANTRIAN = 'cache_fungsi_antrian';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Master app\modules\v1\cache\Cache::getInstalasi()
     * @author yaya
     **/
    const CACHE_INSTALASI = 'cache_instalasi';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Master|Kasir app\modules\v1\cache\Cache::getRuangan()
     * @author yaya
     **/
    const CACHE_RUANGAN = 'cache_ruangan';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Kasir app\modules\v1\cache\Cache::getNilaiUang()
     * @author yaya
     **/
    const CACHE_NILAI_UANG = 'cache_nilai_uang';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Kasir app\modules\v1\cache\Cache::getNilaiUang()
     * @author yaya
     **/
    const CACHE_PEGAWAI_RUANGAN = 'cache_pegawai_ruangan';

    /**
     * @todo menampilkan semua attributes cache untuk fungsi antrian sesuai di tabel
     * @see Service Kasir app\modules\v1\cache\Cache::getNilaiUang()
     * @author yaya
     **/
    const CACHE_SHIFT = 'cache_shift';

    const CACHE_SERVICE_CATEGORY = 'cache_service_category';
    const CACHE_SERVICE_GROUP = 'cache_service_group';
    /**
     * @todo menampilkan semua attributes cache untuk cara bayar sesuai di tabel
     * @see Service Kasir|Master app\modules\v1\cache\Cache::getCaraBayar()
     * @author yaya
     *
     **/
    const CACHE_PERUJUK = 'cache_perujuk';

    /**
     * @todo get riwayat tindakan by tipe_pelayanan
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_TPT = 'TINDAKAN';
    const VAR_TPB = 'BMHP';

    /**
     * @todo const buat nampung igd id
     * @usage Service Master JadwalDokterController
     * @author Rizqi febian
     **/
    const VAR_IGD_ID = '2';

    /**
     * @todo const buat nampung cara pembayaran dari lookup
     * Variable Cara Bayar Tunai
     * @usage Service Kasir TraPembayaranUangMukaController
     * @author Rizqi febian
     **/
    const VAR_CB_T = '31';

    /**
     * @todo approved reseptur lookup
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_AR = 347;

    /**
     * @todo const buat nampung batal reseptur dari lookup
     * Variable Batal Reseptur
     * @usage Service Apotek InfReseptur
     * @author Rizqi febian
     **/
    const VAR_B_R = '432';

    /**
     * @todo const buat nampung cara pembayaran dari lookup
     * Variable Cara Bayar Piutang
     * @usage Service Kasir TraPembayaranUangMukaController
     * @author Rizqi febian
     **/
    const VAR_CB_P = '33';

    /**
     * @todo const buat nampung konfig system
     * Variable Konfig System
     * @usage Service Kasir TraPembayaranUangMukaController
     * @author Rizqi febian
     **/
    const VAR_K_S = 'var_cache_konfig_system';

    const VAR_CACHE_PROFILE_RS_BY_ID = 'var_cache_profile_rs_by_id';

    /**
     * @todo untuk status bayar
     **/
    const LUNAS = 348;
    const BELUM_LUNAS = 349;

    /**
     ** @todo untuk group cara bayar
     **/
    const GROUP_UMUM = 417;
    const GROUP_BPJS = 418;
    const GROUP_JAMINAN = 419;

    /**
     * @todo cara pembayaran di lookup
     **/
    const CARA_TUNAI = 31;
    const CARA_PIUTANG = 33;

    /**
     * @todo untuk keterangan yang hard code
     **/
    const KET_PEMBAYARAN = 'Pembayaran tagihan pasien';

    /**
     * @todo const buat nampung jabatan id kepala ruangan
     * Variable Jabatan Kepala Ruangan
     * @usage Service Pendaftaran InfPasien actionExportPdf
     * @author Rizqi febian
     **/
    const VAR_J_K_R = '3';

    /**
     * @todo for informasi pemesanan kamar
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    // ket tempat tidur isi perempuan
    const VAR_KTTIP = 3;
    // ket tempat tidur isi laki-laki
    const VAR_KTTIL = 4;
    // ket tempat tidur kosong
    const VAR_KTTK = 7;
    // ket tempat tidur kosong laki-laki
    const VAR_KTTKL = 2;
    // ket tempat tidur kosong perempuan
    const VAR_KTTKP = 1;

    /**
     * @todo lookup status booking kamar
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_BTL = '368';
    const VAR_DTLK = '369';
    const VAR_STJ = '370';
    // blm konfirmasi
    const VAR_BK = '371';

    /**
     * @todo lookup jenis kamar
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_JKLK = '340';
    const VAR_JKW = '341';
    const VAR_JKF = '342';
    const VAR_JKC = '431';

    /**
     * @todo lookup jenis kelamin
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_LK = 15;
    const VAR_PR = 16;

    /**
     * @todo menampilkan semua attributes cache untuk kelaspelayanan sesuai di tabel
     * @author rizal
     *
     **/
    const VAR_C_KP = 'cache_kelas_pelayanan';
    /**
     * @todo untuk keperuluan di transaksi pembayaran tagihan pasoen
     **/
    const PASIEN_PULANG = 'pasien_pulang';
    const PASIEN_KARCIS = 'pasien_karcis';
    const PASIEN_PENUNJANG = 'pasien_penunjang';
    const PASIEN_ALKES = 'pasien_alkes';

    /**
     * @todo ini untuk kelompok tindakan karcis
     * @author yaya
     **/
    const VAR_KEL_KRCS = 17;
    const VAR_KEL_PENUNJANG = 19;

    /**
     * @todo var untuk except penunjang
     **/
    public static $exceptPenunjang = [1, 2, 3, 6];

    public static $exceptPenunjangIncludeKamarOperasi = [1, 2, 3, 6, 12];

    /**
     * @todo status pengiriman rekam medis
     **/
    const STATUS_KIRIM = 'status_kirim';

    /**
     * @todo status untuk dokumen sudah di terima
     **/
    const SUDAH_DITERIMA = 421;

    /**
     * @todo status konfirmasi rekam medis
     **/
    const STATUS_KONFIRMASIRM_BELUMPROSES = '664';
    const STATUS_KONFIRMASIRM_PROSES = '665';

    /**
     * @todo const buat nampung data jenis obat alkes
     * Variable Jenis Obat Alkes
     * @usage Service Master Allow actionListJenisObat
     * @author Rizqi febian
     **/
    const VAR_J_OA = 'var_j_oa';

    /**
     * @todo const buat nampung satuan kecil, besar dan sedang
     * Variable Satuan Kecil Besar Sedang
     * @usage Service Master Allow actionListSatuan
     * @author Rizqi febian
     **/
    const VAR_S_KBS = 'var_s_kbs';

    /**
     * @todo const buat nampung group ina cbg
     * @usage Service Master Allow actionListInaCbg
     * @author Budi
     **/
    const VAR_INA_CBG = 'var_ina_cbg';

    /**
     * @todo const buat nampung konfig system
     * Variable Konfig Gudang
     * @usage Service Master Obat Alkes
     * @author Rizqi febian
     **/
    const VAR_K_G = 'var_cache_konfig_system';

    /**
     * @todo cara bayar umum for pasien karcis
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const VAR_UMUM = 5;

    /**
     * @todo const buat nampung data intalasi
     * @author faidzin
     **/
    const INST_ID_RJ = 1;
    const INST_ID_RD = 2;
    const INST_ID_RI = 3;
    const INST_ID_PENUNJANG = [4, 5, 7];
    const INST_ID_LAB = 4;
    const INST_ID_RAD = 5;
    const INST_ID_APT = 6;
    const INST_ID_REHAB = 7;
    const INST_ID_RM = 8;
    const INST_ID_BEDAH = 12;
    const INST_ID_MCU = 21;
    const INSTALASI_SYS_ADMIN = 13;
    const INST_ID_PDF = 10;

    /**
     * @todo const buat nampung cara bayar sesuai master untuk pendaftaran
     * @author faidzin
     **/
    const CB_PEN_UMUM = '5';

    /**
     * @todo const buat nampung hasil tekanan darah
     * Variable Cache Hasil Tekanan Darah
     * @usage Service Ranap AllowController
     * @author Rizqi febian
     **/
    const VAR_H_TD = 'var_cache_konfig_system';

    /**
     *
     * @var description : variable cache data via bagiantubuhdetail_m
     * @return multidimensional array: contains all columns
     * @example [0 => ['records_1', 'records_2'], 1 => ['records_14']]
     * @see BE: ranap/modules/v1/controllers/AllowController/ || public function actionAllowGetDataFisik()
     *
     */
    const VAR_CACHE_DETAILBAGIANTUBUH = 'VAR_CACHE_DETAILBAGIANTUBUH';

    /**

     * @todo const buat nampung keterangan tempat tidur
     * @author faidzin
     **/
    const KET_TT_KSG_P = '1'; // kosong perempuan
    const KET_TT_KSG_L = '2'; // kosong laki2
    const KET_TT_ISI_P = '3'; // isi perempuan
    const KET_TT_ISI_L = '4'; // isi laki2
    const KET_TT_BRSH = '5'; // dibersihkan
    const KET_TT_PESAN = '6'; // dipesan
    const KET_TT_KSG = '7'; // kosong flexibel
    const KET_TT_KSG_CMPR = '8'; // kosong campur
    const KET_TT_NEED_CLEAN = 'need_to_be_clean'; // Need to be Clean
    const KET_TT_RELEASE_NOT_VACATED = 'release_not_vacated';
    const KET_TT_ALTD_NOT_OCCUPIED = 'allotted_but_not_occupied';
    const KET_TT_VCT = '9'; // vacant (MHG)
    const KET_TT_OCC = '11';
    const KET_TT_ALTD_NOT_OCCUPIED_ID = '14';

    /**
     * @todo Kelompok tindakan lab & radiologi
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const KEL_TIN_LAB = 26;
    const KEL_TIN_RAD = 10;

    const BELUM_SETUJU = 470;
    const VAR_CACHE_GOLONGANUMUR = 'var_cache_golongan_umur';
    /** Default Antrian penunjang **/
    const D_PENUNJANG = 328;
    const D_RAJAL = 321;
    const LAB_BELUM_PERIKSA = 477;

    /**
     * @todo CPPT Terapi Tindakan
     * @author Ardi Pratama <ardi@docotel.com>
     **/
    const VAR_CACHE_LOOKUP_JENISINSTRUKSI = 'VAR_CACHE_LOOKUP_JENISINSTRUKSI';

    /**
     * @todo const buat nampung caramasuk_id,
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_CM_RAJAL = 1;
    const VAR_CM_IGD = 2;

    /**
     * @todo const buat nampung status_masuk,
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_SM_R = "358";
    const VAR_SM_NR = "359";

    /**
     * @todo const status_periksa,
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_SP_AP = "1"; //antrian poli
    const VAR_SP_AK = "430"; //antrian kasir
    const VAR_SP_BP = "486"; //belum periksa
    const VAR_SP_PEN = "9999"; //antrian penunjang ketika penjamin != perorangan
    const VAR_SP_AD = 339; // status periksa default
    const VAR_KUN_SM = 9999; // default status masuk

    /**
     * @todo const status_pasien,
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_PAS_B = "310"; //pasien baru
    const VAR_PAS_L = "311"; //pasien lama

    /**
     * @todo const kunjungan,
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_K_L = "181"; //kunjungan lama
    const VAR_K_B = "180"; //kunjungan baru

    /**
     * @todo const penjamin perseorangan
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_P_P = "1";

    /**
     * @todo const status lab belom periksa
     * @usage Service Pendaftaran Pendaftaran
     * @author Rizqi febian
     **/
    const VAR_SL_BP = "477";

    /**
     * @todo const status permintaan konsul
     * @usage Transaksi permintaan and informasi
     * @author Iqbal@docotel.com
     **/
    const STATUS_PERMINTAAN_KONSUL_BATAL = '439';
    const STATUS_PERMINTAAN_KONSUL_TIDAK_SETUJU = '438';
    const STATUS_PERMINTAAN_KONSUL_SETUJU = '437';
    const STATUS_PERMINTAAN_KONSUL_DEFAULT = '9999';

    const JNS_KNSL_1X = 434; // jenis konsul 1 kali
    const JNS_KNSL_RB = 435; // jenis konsul rawat bersama
    const JNS_KNSL_AR = 436; // jenis konsul alih rawat
    const JNS_KNSL_AR_RB = 1437; // jenis konsul alih rawat dan rawat bersama

    /**
     * @todo status lab ambil sample
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const ST_SMPL = '474';
    /**
     * pasien rujukan lab
     */
    const BLM_PERIKSA = 477;
    const PENJAMIN_ID = 1; // pembayaran perorangan
    const DISETUJUI = 471; // status di setujui
    const BLM_OPERASI = 488;
    const RESCHEDULE = 692;

    /**
     * pasien lab
     */
    const DI_TOLAK = 541;
    const BTL_PERIKSA_LAB = 476;
    const BTL_APPROVE = 472; // status batal dari status penunjang

    /**
     * @todo status lab
     * @author Randy Vianda Putra <randy@docotel.com>
     **/
    const ST_PERIKSA = '473';
    const ST_SELESAI = '475';

    /**
     * const instalasi penunjang
     */
    const VAR_I_LAB = '4';
    const VAR_I_RAD = '5';
    const VAR_I_REHAB = '7';
    const VAR_I_BED = '12';
    const VAR_I_MCU = '21';
    /**
     * const
     */


    /**
     * const
     */
    const J_INST_TIND = '457';
    const J_INST_OBAT = '458';
    const J_INST_PNJG = '459';
    const J_INST_UDD = '1040';

    static $asal_rujukan = [
        'order' => 'Order',
        'rujukanRS' => 'Rujukan Rs',
        'aps' => 'APS'
    ];


    const INSTALASI_BEDAH = 12;

    /**
     * @author:
     * @todo const status periksa lookup
     **/
    const STATUS_PERIKSA_ANTR_POLI = 1;
    const STATUS_PERIKSA_ANTR_PENDFTR = 339;
    const STATUS_PERIKSA_ANTR_KASIR = 430;
    const STATUS_PERIKSA_SDH_PERIKSA = 3;
    const STATUS_PERIKSA_DIPERIKSA = 2;
    const STATUS_PERIKSA_PULANG = 4;
    const STATUS_PERIKSA_BTL_PERIKSA = 402;
    const STATUS_PERIKSA_RUJUK_RANAP = 433;
    const STATUS_PERIKSA_BTL_KONSUL = 411;
    const STATUS_PERIKSA_SET_DOKTER = 540;
    const STATUS_PERIKSA_BLM_PERIKSA = 486;
    const STATUS_PERIKSA_BLM_DIJAWAB = 670;
    const STATUS_PERIKSA_SDH_DIJAWAB = 671;
    const STATUS_PERIKSA_MCU_SELESAI = 1148;

    const STATUS_PERIKSA_NAMA_SDH_DIJAWAB = 'SUDAH DIJAWAB';
    const STATUS_PERIKSA_NAMA_BATAL_KONSULTASI = 'Batal Konsultasi';

    /**
     * @todo const penunjang sedang operasi
     * @usage Service Bedah InfPasienOperasi
     * @author Rizqi febian
     **/
    const VAR_P_SO = "482";


    const STATUS_ANTRIAN_POLI = 1;  //Poliklinik
    const STATUS_PERIKSA = 2; //Diperiksa
    const STATUS_PULANG = 4; //Pulang
    const STATUS_BATAL_PERIKSA = 402; //Batal Periksa
    const STATUS_RUJUK_RAWAT_INAP = 433; //Rujuk Rawat Inap

    const VAR_I_RJ = 1; // instalasi rawat jalan
    const VAR_I_RD = 2; // instalasi rawat darurat
    const VAR_I_RANAP = 3; // instalasi rawat inap

    const STATUS_RANAP_BELUM_PERIKSA = 440;
    const STATUS_RANAP_PERIKSA = 441;
    const STATUS_RANAP_BATAL_RAWAT = 453;
    const STATUS_RANAP_PULANG = 487;

    const PENJAMIN_UMUM = 5;
    const PENJAMIN_ASURANSI = 2;
    const PENJAMIN_PERUSAHAAN = 7;
    const PENJAMIN_BPJS = 6;
    const PENJAMIN_RK = 41;
    const PENJAMIN_PERSONIL = 45;

    // status pengajuan klaim
    const STATUS_BELUM_KOREKSI = 549;
    const STATUS_SUDAH_KOREKSI = 550;
    const STATUS_PROSES_KLAIM = 556;
    const STATUS_FINAL_KLAIM = 551;

    // status koreksi IDRG
    const STATUS_KOREKSI_IDRG = 2500;
    const STATUS_FINAL_IDRG = 2501;

    const STATUS_INACBG_KOREKSI = 2502;
    const STATUS_INACBG_GROUPING = 2503;
    const STATUS_INACBG_FINAL = 2504;

    // MODUL ASURANSI (Menggunakan Instalasi Pendaftaran)
    const MODUL_EKLAIM = 24;
    const INSTALASI_EKLAIM = 10;
    const RUANGAN_EKLAIM = 5;

    // const lookup untuk fungsi antrian farmasi
    const ANT_DA = 323;
    const ANT_R = 324;
    const ANT_NR = 325;
    const ANT_ACB = 326;

    // jenis dokter (kebutuhan dokter igd)
    const JNS_DKTR_KNSL = 484;
    const JNS_DKTR_PJ = 485;

    /**
     * @author: sgtarfmmnndr
     * @todo const jabatan id untuk kepala ruangan
     **/
    const VAR_JABATAN_KEPALA_RUANGAN = 3;

    /**
     * @todo const penunjang sudah operasi
     * @usage Service Bedah IntraOperasiController
     * @author Rizqi febian
     **/
    const VAR_P_SdhO = "483";

    // status periksa penunjang
    const ST_P_PEN_BLM_PRKS = 477; // belum periksa
    const ST_P_PEN_BLM_OPRS = 488; // belum operasi
    const ST_P_PEN_PRKS = 473; // periksa
    const ST_P_PEN_BTL = 476; // batal
    const ST_P_PEN_AMB_SAMP = 474; // ambil sample
    const ST_P_PEN_SDG_OPRS = 482; // sedang operasi
    const ST_P_PEN_SELESAI = 475; // selesai
    const ST_P_PEN_SDH_OPRS = 483; // sudah operasi
    const ST_P_PEN_DISERAHKAN = 483; // di serahkan

    // status periksa ranap
    const ST_P_RNP_BLM_PRKS = 440; // belum periksa
    const ST_P_RNP_PRKS = 441; // periksa
    const ST_P_RNP_PLG = 487; // pulang
    const ST_P_RNP_BTL = 453; // batal

    const RESEPTUR_BELUM_DIPROSES = 346;
    const RESEPTUR_SUDAH_DIPROSES = 347;
    const IMPLEMENTASI_SUDAH_IMPLEMENTASI = 455;
    const RESEPTUR_DISERAHKAN = 660;
    const RESEPTUR_DIBATALKAN = 432;

    // status worklist
    const WORKLIST_BELUM_DISIAPKAN  = 674;
    const WORKLIST_DISIAPKAN        = 675;
    const WORKLIST_DITELAAH         = 676;
    const WORKLIST_QC               = 677;
    const WORKLIST_SIAP_DISERAHKAN  = 678;

    public static $WORKLIST_STATUS = [
        self::WORKLIST_BELUM_DISIAPKAN, #657
        self::WORKLIST_DITELAAH,        #676
        self::WORKLIST_DISIAPKAN,       #675
        self::WORKLIST_QC,              #677
        self::WORKLIST_SIAP_DISERAHKAN, #678
    ];

    // konfig worklist
    const WORKLIST_KONFIG_FILTER_RUANGAN = 'konfig_worklist_filter_ruangan';
    const WORKLIST_KONFIG_URUTAN_PERIKSA = 'konfig_worklist_filter_urutan_periksa';

    // konfig eklaim dokter multiple
    const EKLAIM_DOKTER_MULTIPLE = 'eklaim_dokter_multiple';
    // konfig stok obat alkes
    const KONFIG_VALIDASI_STOK_OBAT_ALKES = 'konfig_validasi_stok_obat_alkes';

    // Konfigurasi untuk disable button view gambar hasil radiologi, jika pemeriksaan belum selesai / dibatalkan
    const KONFIG_DISABLE_BUTTON_HASIL_RADIOLOGI = 'konfig_disable_button_hasil_radiologi';

    // konfig ftp nas
    const KONFIG_FTP_NAS = 'konfigftp_nas';

    /**
     * @author: sgtarfmmnndr
     * @todo const lookup id untuk keterangan pemberian obat
     **/
    const VAR_KET_PO_TIDAK_DIBERIKAN = 466;
    const VAR_KET_PO_MUNTAH = 467;
    const VAR_KET_PO_PUASA = 468;
    const VAR_KET_PO_PASIEN_MENOLAK = 469;
    const VAR_KET_PO_DIKONSUMSI = 489;

    /**
     * status verivikasi
     **/
    public static $status_verif = [
        549 => 'Belum Dikoreksi',
        550 => 'Sudah Dikoreksi',
        556 => 'Proses Klaim',
        551 => 'Sudah Final Klaim'
    ];

    /**
     * Mapping Kelompok diagnosa
     **/
    public static $mapp_kel_diagnosa = [
        'Utama' => 2,
        'Penyerta' => 3,
        'Masuk' => 1,
        'Terapi' => 6,
    ];

    /**
     * Mapping New Kelompok diagnosa
     **/
    public static $_mapp_kel_diagnosa = [
        'diagnosa_utama' => 2,
        'diagnosa_tambahan' => 3,
        'diagnosa_masuk' => 1,
        'tindakan' => 6,
    ];
    /*
    * @author: sgtarfmmnndr
    * @todo const penomoran id untuk penomoran permintaan retur obat
    **/
    const VAR_PENOMORAN_PERMINTAAN_RETUR = 152;

    const SUDAH_KOREKSI = 550;
    const PROSES_KLAIM = 556; //const lookup status verifikasi, value = proses klaim

    const VAR_KEL_BAR = 'var_kelompok_barang';
    const VAR_SUB_KEL = 'var_sub_kelompok_barang';
    const INSTALASI_GUDANG_UMUM = 15;
    const INSTALASI_GUDANG_FARMASI = 66;

    /**
     * Penjamin Asuransi
     **/

    const BELUM_PENGAJUAN = 552;
    const PROSES_PEMBAYARAN = 553;
    const SUDAH_PEMBAYARAN = 554;
    const BATAL_PEMBAYARAN = 555;

    const BPJS_KEY = "3fd0e9c73e27d200188f7f3d57e4a60ff656c85c796610366aea2a765c06c701";
    const INACBG_TARIF = "1";
    const INACBG_NIK = "123123123123";
    const INACBG_TARIF_KODE = "AP";

    public static $statusAlokasi = [
        1 => 'Sudah Alokasi',
        0 => 'Belum Alokasi'
    ];
    const CARA_BAYAR_ASURANSI = 2;
    const CARA_BAYAR_BPJS = 6;
    const CARA_BAYAR_UMUM = 5;

    const LIST_IDENTITAS_PASIEN = [
        '1' => 'NIK',
        '2' => 'KITAS',
        '3' => 'Paspor',
        '4' => 'Kartu JKN',
        '5' => 'Lainnya',
    ];

    const LIST_STATUS_COVID = [
        'ODP' => 'ODP',
        'PDP' => 'PDP',
        'Terkonfirmasi' => 'Terkonfirmasi',
    ];

    const PAYOR_CD = [
        '71' => 'COVID-19',
        '72' => 'KIPI',
        '73' => 'BBL ',
        '74' => 'PMR',
        '75' => 'CO-INS',
        '76' => 'JPS',
    ];

    const PAYOR_ID_COVID = 71;
    const PAYOR_ID_KIPI = 72;
    const PAYOR_ID_BAYI_BARU_LAHIR = 73;
    const PAYOR_ID_PERPANJANGAN_MASA_RAWAT = 74;
    const PAYOR_ID_COINSIDENSE = 75;
    const PAYOR_ID_JAMPERSAL = 76;
    const PAYOR_ID_JKN = 3;




    /**
     * @todo Konstan kelompok diagnosa
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/

    const VAR_KELOMPOK_DIAGNOSA_MASUK = 1; // kelompok diagnosa terapi
    const VAR_KELOMPOK_DIAGNOSA_UTAMA = 2; // kelompok diagnosa utama
    const VAR_KELOMPOK_DIAGNOSA_PENYERTA = 3; // kelompok diagnosa penyerta
    const VAR_KELOMPOK_DIAGNOSA_OPERASI = 4; // kelompok diagnosa operasi
    const VAR_KELOMPOK_DIAGNOSA_KELUARGA = 5; // kelompok diagnosa keluarga
    const VAR_KELOMPOK_DIAGNOSA_TERAPI = 6; // kelompok diagnosa terapi
    const VAR_KELOMPOK_DIAGNOSA_AWALAN = 7; // kelompok diagnosa awalan

    const VAR_KELOMPOK_DIAGNOSA_KERJA = 9; // kelompok diagnosa kerja
    const VAR_KELOMPOK_DIAGNOSA_FUNGSI = 10; // kelompok diagnosa kerja

    public static $statusAsal = [
        'REKOMENDASI' => 'Rekomendasi Order',
        'PO_MANUAL' => 'PO Manual',
        'permintaan_pembelian_barang'  => 'Permintaan Pembelian Barang',
        'PURCHASE REQUEST' => 'Purchase Request'
    ];

    /** Kebutuhan untuk di pengadaan **/
    const JENIS_OBAT = 'obat';
    const JENIS_BARANG = 'barang';
    const JENIS_BARANG_OBAT = 'barang-obat';
    const STATUS_BATAL_PO = 575;

    const VAR_R_JNZ = 38; //variable id ruangan jenazah
    const VAR_I_JNZ = 16; //variable id instalasi jenazah
    const VAR_BLM_DTRM_JNZ = 581; //variable jenazah belum diterima

    const PO_BELUM_DITERIMA = 572;
    const BELUM_SELESAI_PO = 573;
    const SUDAH_DITERIMA_PO = 574;
    const CLOSING_PO = 580;
    const PO_EXPIRED = 686;

    /** Untuk default kadaluarsa **/
    const DEFAULT_EXPIRED = "2050-12-31";

    /** Untuk status jenazah **/
    const STATUS_BELUM_DITERIMA_JNZ = 581;
    const STATUS_DITERIMA_JNZ = 577;

    /**
     * @todo Konstan Status Asesmen Skrining Gizi
     * @author Ardi Pratama <ardi@docotel.com>
     **/
    const STATUSSKRININGGIZI_TIDAK = 80;
    const STATUSSKRININGGIZI_BELUM = 81;
    const STATUSSKRININGGIZI_SUDAH = 82;

    /**
     * @todo Konstan tipe icd diagnosa
     * @author Rizal Faidin <rizal@docotel.com>
     **/
    const ICD_9 = 'ICD IX';
    const ICD_10 = 'ICD X';

    const CARA_KELUAR_RUJUK_RAWAT_INAP = 5; //from cara keluar
    const EMERGENCY = 'EMERGENCY';
    const NON_EMERGENCY = 'NON EMERGENCY';

    // status ambulance
    const AMBULAN_IDLE = 591;

    /**
     * @todo Konstan skrining gizi penurunan berat badan
     * @author Rizal Faidin <rizal@docotel.com>
     **/
    const BB_DIRENCANAKAN = [
        0 => 'Tidak Terjadi',
        1 => 'Tidak Yakin',
        2 => 'Ya, Turun',
    ];

    /**
     * @todo Konstan key untuk daftar dan login mobile app
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_DAFTAR_KEY = 'ErDZJm3v8gmZVS7j';
    const VAR_LOGIN_KEY = 'HIg60jt5gYkckOND';

    /**
     * @todo Konstan tipe lookup
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    const VAR_LOOKUP_TYPE_AGAMA = 'agama';
    const VAR_LOOKUP_TYPE_JENIS_KELAMIN = 'jenis_kelamin';
    const VAR_LOOKUP_TYPE_STATUS_DAFTAR_OL = 'status_daftar_ol';

    /**
     * @todo Konstan status pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES = 564; // status pendaftaran online belum diproses
    const VAR_STATUS_DAFTAR_OL_DISETUJUI = 565; // status pendaftaran online disetujui
    const VAR_STATUS_DAFTAR_OL_DITOLAK = 566; // status pendaftaran online ditolak

    /**
     * @todo Konstan id cara bayar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_ID_CARABAYAR_UMUM = 1; // id cara bayar pribadi
    const VAR_ID_CARABAYAR_BPJS = 6; // id cara bayar bpjs
    const VAR_ID_CARABAYAR_ASURANSI = 2; // id cara bayar asuransi

    /**
     * @todo Konstan status antrian farmasi
     * @author ali.padilah@docotel.com
     **/

    const VAR_SF_1 = 583; // Belum Proses
    const VAR_SF_2 = 584; // Sedang Proses
    const VAR_SF_3 = 585; // Siap Ambil
    const VAR_SF_4 = 586; // Selesai

    const INSTALASI_FARMASI = 6;

    /**
     * @todo Konstan cron middleware
     * @author ali.padilah@docotel.com
     **/

    const SYNC_PASIEN = 5;
    const SYNC_PENDAFTARAN = 22;

    const PENERIMAAN_VERIF = 1;
    const PENERIMAAN_BATAL = 2;

    public static $statusPenerimaan = [
        0 => 'Belum Verifikasi',
        1 => 'Sudah Verifikasi',
        2 => 'Batal'
    ];

    /**
     * @todo Konstan id kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_ID_KUOTA_ANTRIAN_POLIKLINIK = 597; // id kuota antrian poliklinik
    const VAR_ID_KUOTA_ANTRIAN_DOKTER = 598; // id kuota antrian dokter
    const VAR_ID_TANPA_KUOTA = 599; // id tanpa antrian kuota

    /**
     * @todo Konstan lookup type kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    const VAR_LOOKUP_TYPE_KUOTA_ANTRIAN = 'kuota_antrian';

    /**
     * @todo Konfigurasi ID  base integrasi for akunting
     * @author Arif Priatna <arif.priatna@docotel.com>
     **/
    const KONFIG_ID = 1;
    const VOUCHER_TYPE = 'DINVOICE';
    const TAX = 'tax';
    const PAYTERM = 'payterm';
    const BANK = 'bank';

    public static $crud = [
        'CREATE' => 'create',
        'UPDATE' => 'update',
        'DELETE' => 'delete'
    ];

    /* kebutuhan untuk integrasi adjusment*/
    const ADJ = 'ADJ';
    const ADJM = 'ADJM';
    const ADJK = 'ADJK';

    /**
     * @todo Konstan kelas pelayanan
     * @author budi@docotel.com
     **/

    const KELAS_3 = 3; //dev=3 //mhkn=6
    const RUANGAN_AMBULAN  = 34;

    /**
     * @todo Konstan penjamin
     * @author budi@docotel.com
     **/

    const NEW_PENJAMIN_UMUM = 1;
    const BATAL_PESAN = 602;
    const BATAL_PESAN_AMBULAN = 608;
    const BELUM_PROSES = 606;

    const BELUM_DIKIRIM = 398;
    const SUDAH_DIKIRIM = 399;

    public static $statusPesan = [
        self::BELUM_DIKIRIM => 'Belum Dikirim',
        self::SUDAH_DIKIRIM => 'Sudah Dikirim',
        self::BATAL_PESAN => 'Batal'
    ];

    const PESAN_AMBULAN = 592;
    const AMBULAN_SEDANG_DIPAKAI = 593;
    const AMBULAN_SUDAH_DIPROSES = 607;

    const SATUAN_TINDAKAN_KALI = 351;
    const SPD_BELUM_DIPROSES = 609;


    const STAT_RM_AKTIF = 336;
    const STAT_RM_TDK_AKTIF = 337;
    const STAT_RM_EXPIRED = 338;

    const NAMA_BIN_BAYI = 206;

    const OPENING_ANTRIAN = 'dingdong1';

    // ruangan pendaftaran
    const RUANGAN_PENDAFTARAN_RANAP = 11;
    const RUANGAN_PENDAFTARAN_IGD = 18;
    const RUANGAN_PENDAFTARAN_PENUNJANG = 28;
    const RUANGAN_MEDICAL_CHECKUP = 323;

    // log activity
    const LA_AKSI_TAMBAH = 'tambah';
    const LA_AKSI_HAPUS = 'hapus';
    const LA_AKSI_EDIT = 'edit';
    const LA_AKSI_MERGE = 'merge';

    const LA_TIPE_TINDAKAN = 'tindakan';
    const LA_TIPE_BMHP = 'BMHP';
    const LA_TIPE_PAKET = 'Paket';
    const LA_TIPE_PENJAMIN = 'Penjamin';
    const LA_TIPE_Catatan = 'Catatan';
    const LA_TIPE_PO = 'PO';
    const LA_TIPE_PO_NONMEDIS = 'PONONMEDIS';
    const LA_TIPE_AKOMODASI = 'Akomodasi';

    /** AKOMODASI */
    const AKOMODASI_MUTASI = 'akomodasi_mutasi';
    const STOP_AKOMODASI = 'stop_akomodasi';
    const LIHAT_AKOMODASI = 'lihat_akomodasi';
    const STOP_TITIPAN = 'stop_titipan';

    /** TIPE TRANSAKSI */
    const TIPE_TRANSAKSI_VENDOR = 700;
    const TIPE_TRANSAKSI_KARYAWAN = 701;
    const TIPE_TRANSAKSI_PASIEN = 702;

    /** JENIS TRANSAKSI */
    const JENIS_TRANSAKSI_PEMASUKAN = 668;
    const JENIS_TRANSAKSI_PENGELUARAN = 669;
    /** Status verifikasi pemesanan obat alkes **/
    const OBAT_SUDAH_DIVERIFIKASI = 667;
    const OBAT_BELUM_DIVERIFIKASI = 666;

    const BAYAR_TUNAI = 27;
    const BAYAR_NONTUNAI = 28;
    const TIM_OPERASI_DOKTER_BEDAH = 508;

    const LIMIT_INFINITY_SCROLL = 10;

    const RJAL = "1";
    const RINP = "3";
    public static $status_kunjungan = [
        0 => 'Belum Koreksi',
        1 => 'Sudah Koreksi',
        2 => 'Proses Klaim',
        3 => 'Final Klaim'
    ];

    public static $status_verifikasi = [
        'Belum Verifikasi' => 'Belum Verifikasi',
        'Sudah Verifikasi' => 'Sudah Verifikasi',
    ];

    public static $mapp_kel_diagnosa_penjaminasuransi = [
        'Utama' => 1,
        'Tambahan' => 2,
        'Sebab Luar' => 4,
        'Tindakan/Operasi' => 3,
        'Morfologi' => 5,
    ];

    const DIAGNOSA_UTAMA = "Diagnosa Utama"; // kelompok diagnosa utama
    const DIAGNOSA_TAMBAHAN = "Diagnosa Tambahan"; // kelompok diagnosa tambahan
    const DIAGNOSA_OPERTINDAKAN = "Diagnosa Opertindakan"; // kelompok diagnosa tindakan / operasi
    const DIAGNOSA_LUAR = "Diagnosa Luar"; // kelompok diagnosa sebab luar
    const DIAGNOSA_MORFOLOGI = "Diagnosa Morfologi"; // kelompok diagnosa morfologi
    const DIAGNOSA_K = "K";
    const DIAGNOSA_PENYERTA = "Diagnosa Penyerta";
    const DIAGNOSA_TERAPI = "Terapi";

    const NAME_DIAGNOSA_UTAMA = "diagnosa_utama"; // kelompok diagnosa utama
    const NAME_DIAGNOSA_TAMBAHAN = "diagnosa_tambahan"; // kelompok diagnosa tambahan
    const NAME_DIAGNOSA_OPERTINDAKAN = "diagnosa_opertindakan"; // kelompok diagnosa tindakan / operasi
    const NAME_DIAGNOSA_LUAR = "diagnosa_luar"; // kelompok diagnosa sebab luar
    const NAME_DIAGNOSA_MORFOLOGI = "diagnosa_morfologi"; // kelompok diagnosa morfologi

    const MAP_DIAGNOSA_UTAMA = 1; // kelompok diagnosa utama
    const MAP_DIAGNOSA_TAMBAHAN = 2; // kelompok diagnosa tambahan
    const MAP_DIAGNOSA_OPERTINDAKAN = 3; // kelompok diagnosa tindakan / operasi
    const MAP_DIAGNOSA_LUAR = 4; // kelompok diagnosa sebab luar
    const MAP_DIAGNOSA_MORFOLOGI = 5; // kelompok diagnosa morfologi

    // status konsul poli
    const STATUS_KONSUL_DIJAWAB = 671;
    const STATUS_KONSUL_BLM_DIJAWAB = 670;
    const T_K_KONSUL_POLI = 672;
    const T_K_RENCANA_KONTROL = 673;

    /**
     * @todo Konstan Rate Limiter
     * @author rizqi@docotel.com
     **/
    const RATELIMITER_INSECONDS = 12;
    const RATELIMITER_NUMBEROFREQUEST = 2;

    /**
     * @todo Konstan Public Authorization
     * @author rizqi@docotel.com
     **/
    const PUB_AUTH_VALIDTIME = 5;
    const PUB_AUTH_EXPIRETIME = 302400;

    // group jenis obat
    const GROUP_JENIS_OBAT = 'group_jenisobat';
    const BMHP_BELUM_VERIFIKASI = 679;
    const BMHP_SUDAH_VERIFIKASI = 680;

    // status worklist
    const WL_BELUM_DISIAPKAN    = 674;
    const WL_DISIAPKAN          = 675;
    const WL_DITELAAH           = 676;
    const WL_QC                 = 677;
    const WL_SIAP_DISERAHKAN    = 678;
    const IS_RACIKAN = 'Racikan';
    const IS_NON_RACIKAN = 'Non Racikan';

    // racikan master
    const ID_RACIKAN = 1;
    const ID_NON_RACIKAN = 2;

    // status tarif jarak
    const TARIF_DEKAT  = 'DMYX021537';
    const TARIF_SEDANG = 'DMYX021538';
    const TARIF_JAUH   = 'DMYX021539';

    // Kelompok Tindakan
    const KT_BEDAH_SENTRAL = 3;
    const KT_KONSULTASI = 12;
    const KT_VISITE = 32;
    const KT_LABORATORIUM = 26;
    const KT_RADIOLOGI = 10;
    const KT_ADMINISTRATIVE = 17;

    // status tarif jarak
    const DEFAULT_PENJAMIN_UMUM_AMBULAN = 1;

    //const group jenis obat alkes
    const GROUP_JENISOBAT_ALKES = 620;
    const GROUP_JENISOBAT_OBAT = 619;

    //const status PR
    const VAR_SUDAH_PO = 713;
    const VAR_BELUM_PO = 712;
    const VAR_PO_SEBAGIAN = 719;
    const VAR_CANCEL_PR = 720;
    const VAR_APPROVED = 1056;
    const VAR_BELUM_APPROVED = 1057;

    // jabatan signature cetak po
    const DIREKTUR = 'direktur';
    const HEAD_OF_PURCHASING = 'head_purchasing';
    const HEAD_OF_APOTEKER = 'head_apoteker';
    const HEAD_OF_FINANCE = 'head_finance';

    //** Kode Lainnya Prop,Kab,kec */
    const ALAMAT_LAINNYA = 99999;

    //** Status Pekawinan */
    const UNKNOWN = 721;

    const MODULE_LAB_ID = 518;
    const MODULE_FARMASI_ID = 21;
    const MODULE_RAD_ID = 426;

    // const ruangan
    const RUANGAN_THT = 21;
    const RUANGAN_MATA = 4;

    // ** Jenis pendaftaran */
    const J_P_L = 726;
    const J_P_O = 727;

    //const jenis invoice / jenis kwitansi
    const INV_TOTAL = 1;
    const INV_PENJAMIN = 2;
    const INV_PASIEN = 3;

    const GIZI_MODULE_ID = 963;

    //** Jenis Kelamin Lainnya */
    const J_K_LAINNYA = 728;

    //Kode Transaksi Jasa Asuhan Gizi
    const K_T_JAG = 'JasaAsuhanGizi';

    //** Tipe Laporan Rm*/
    const L_T_SOAP_DOKTER = 729;
    const L_T_RESUME_DOKTER = 730;

    // Konstan jenis identitas
    const CONS_ID_KTP = 94;

    // jenis identitas KTP
    const IDENTITAS_KTP = 94;
    const IDENTITAS_BPJS = 601;

    //visit dokter slug
    const VISIT_DOKTER_SLUG = 'VisitDokterSpesialis';

    // Konstan flagging soap fisioterapi ranap
    const TIPE_FISIO_RANAP = 'RI-SOAPFISIO';

    /*
    * Const untuk atur ordering worklist pasien
    */
    const UrutanStatusAntrian = [
        self::STATUS_PERIKSA_ANTR_PENDFTR,
        self::STATUS_PERIKSA_ANTR_KASIR,
        self::STATUS_PERIKSA_ANTR_POLI,
        self::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES,
    ];

    const UrutanStatusPeriksa = [
        self::STATUS_PERIKSA_BLM_PERIKSA,
        self::STATUS_PERIKSA_DIPERIKSA,
        self::STATUS_PERIKSA_SDH_PERIKSA,
        self::STATUS_PERIKSA_RUJUK_RANAP,
        self::STATUS_PERIKSA_PULANG,
        self::STATUS_PERIKSA_BTL_KONSUL,
        self::STATUS_PERIKSA_BTL_KUNJ,
        self::STATUS_PERIKSA_BTL_PERIKSA,
        self::STATUS_RANAP_BELUM_PERIKSA,
        self::STATUS_RANAP_PERIKSA,
        self::STATUS_RANAP_BATAL_RAWAT,
        self::STATUS_RANAP_PULANG,
        self::ST_P_PEN_SDH_OPRS,
        self::ST_P_PEN_SDG_OPRS,
        self::ST_P_PEN_BLM_OPRS,
        self::ST_P_PEN_BTL,
        self::VAR_STATUS_DAFTAR_OL_DISETUJUI,
        self::VAR_STATUS_DAFTAR_OL_DITOLAK,
        self::ST_P_PEN_AMB_SAMP,
        self::ST_P_PEN_SELESAI,
        self::STATUS_PERIKSA_SET_DOKTER,
        self::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
    ];

    const UrutanStatusAntrianRajal = [
        self::STATUS_PERIKSA_ANTR_PENDFTR,
        self::STATUS_PERIKSA_ANTR_POLI,
    ];

    const UrutanStatusPeriksaRajal = [
        self::STATUS_PERIKSA_BLM_PERIKSA,
        self::STATUS_PERIKSA_DIPERIKSA,
        self::STATUS_PERIKSA_RUJUK_RANAP,
        self::STATUS_PERIKSA_PULANG,
        self::STATUS_PERIKSA_BTL_KONSUL,
        self::STATUS_PERIKSA_BTL_KUNJ,
        self::STATUS_PERIKSA_BTL_PERIKSA,
        self::STATUS_PERIKSA_BLM_DIJAWAB,
        self::STATUS_PERIKSA_SDH_DIJAWAB
    ];

    const UrutanStatusAntrianIgd = [
        self::STATUS_PERIKSA_ANTR_POLI,
    ];

    const UrutanStatusPeriksaIgd = [
        self::STATUS_PERIKSA_BLM_PERIKSA,
        self::STATUS_PERIKSA_DIPERIKSA,
        self::STATUS_PERIKSA_RUJUK_RANAP,
        self::STATUS_PERIKSA_PULANG,
        self::STATUS_PERIKSA_BTL_KUNJ,
        self::STATUS_PERIKSA_BTL_PERIKSA,
        self::STATUS_RANAP_BATAL_RAWAT,
        self::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
    ];


    const UrutanStatusAntrianRanap = [

    ];

    const UrutanStatusPeriksaRanap = [
        self::STATUS_RANAP_BELUM_PERIKSA,
        self::STATUS_RANAP_PERIKSA,
        self::STATUS_RANAP_BATAL_RAWAT,
        self::STATUS_RANAP_PULANG,
    ];

    const UrutanStatusAntrianOt = [

    ];

    const UrutanStatusPeriksaOt = [
        self::ST_P_PEN_SDH_OPRS,
        self::ST_P_PEN_SDG_OPRS,
        self::ST_P_PEN_BLM_OPRS,
        self::ST_P_PEN_BTL,
        self::ST_P_PEN_AMB_SAMP,
        self::ST_P_PEN_BLM_PRKS,
        self::ST_P_PEN_SELESAI,
        self::ST_P_PEN_PRKS,
        self::STATUS_PERIKSA_SUDAH_DIPERIKSA,
        self::STATUS_PERIKSA,
        self::ST_P_PEN_DISERAHKAN ,
    ];

    const UrutanStatusAntrianOl = [
        self::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES,
    ];

    const UrutanStatusPeriksaOl = [
        self::VAR_STATUS_DAFTAR_OL_DISETUJUI,
        self::VAR_STATUS_DAFTAR_OL_DITOLAK,
    ];

    const UrutanStatusPeriksaOlDokter = [
        self::VAR_STATUS_DAFTAR_OL_DISETUJUI,
    ];

    const UrutanStatusPeriksaMcu = [
        self::STATUS_PERIKSA_ANTR_POLI,
        self::STATUS_PERIKSA_DIPERIKSA,
        self::STATUS_PERIKSA_PULANG,
        self::STATUS_PERIKSA_BTL_KONSUL,
        self::STATUS_PERIKSA_ANTR_KASIR,
        self::STATUS_PERIKSA_RUJUK_RANAP,
    ];

    const UrutanStatusPeriksaKonsul = [
        self::STATUS_PERIKSA_BLM_DIJAWAB,
        self::STATUS_PERIKSA_SDH_DIJAWAB,
        self::STATUS_PERIKSA_BTL_KONSUL
    ];

    const INSTALASI_KASIR = 9;

    const KELOMPOK_MAKANAN = 18; //const untuk handle tindakan dengan kelompok tindakan makanan

    // Posisi Tim Operasi
    const TIM_DOKTER_BEDAH = 508;
    const TIM_DOKTER_ANASTESI = 509;
    const TIM_ASS_ANASTESI1 = 511;
    const TIM_ASS_ANASTESI2 = 512;
    const TIM_ASS_BEDAH1 = 513;
    const TIM_ASS_BEDAH2 = 514;


    const RUANGAN_ID_LAB_KLINIK = 130;

    //visit dokter slug
    const VISIT_DOKTER_JAGA_SLUG = 'VisitDokterJaga';

    // status udd
    const UDD_BELUM_PROSES = 1029;
    const UDD_PROSES = 1030;
    const UDD_DIKIRIM = 1031;
    const UDD_DITERIMA = 1032;
    //jenis tindakan
    const JENIS_TINDAKAN_PELAYANAN = 'pelayanan';
    //kelompok tindakan cathlab
    const KEL_TIND_CATHLAB = 34;
    const JENISOBAT_NARKOTIKA = 'jenisobat_narkotika';


    /**
     *
     * LOOKUP by KODE, get lookup_type = jenis_layanan
     * TD_KELOMPOK = Jenis layanan kelompok tindakan
     * TD_TINDAKAN = Jenis layanan tindakan
     * TD_PAKET = Jenis layanan paket
     * TD_OBAT = Jenis layanan obat
     * TD_KELAS = Jenis layanan Kelas
     *
     **/
    const TD_KELOMPOK = 1025;
    const TD_TINDAKAN = 1026;
    const TD_PAKET = 1027;
    const TD_OBAT = 1028;
    const TD_KELAS = 1033;
    const RUANGAN_PENGADAAN = 57;
    const STATUS_OPEN_MCU = 1041;

    //status dokumen rekam medis
    const DOKRM_REQUEST = 1020;
    const DOKRM_DELIVERY = 1021;
    const DOKRM_ISSUES = 1022;
    const DOKRM_RECEIVE = 1023;
    const DOKRM_RETURN = 1024;
    const DOKRM_REMIND = 1025;
    const VAR_CACHE_CONFIG_TARIF = 'var_cache_config_tarif';
    const FITUR_AKSES = 'fitur_akses';

    //group alkes untuk perawat
    const GOUP_ALKES_PERAWAT_BMHP = 'groupjenisobat_perawat_bmhp';
    const GOUP_ALKES_PERAWAT_RESEP = 'groupjenisobat_perawat_resep';

    const MONITORING_RM_ISSUE = 1022;

    //task id jkn
    const TASK_ANTRI_POLI = 3;

    //jenis reservasi
    const JENIS_RSV_JKN = 1102;
    const JENIS_RSV_ESIANTRI = 1310;

    const GOL_UMUR_SEMUA_UMUR = 11;

    const CACHE_LOOKUP_TRANSAKSI = 'var_cache_lookup_transaksi';

    const GET_PROFILE_RS = 'profile-rs';
    const GET_PROFILE_RS_VIEW = 'profile-rs-view';

    const RM_MODULE_ID = 22;
    const STATUS_CANCELED_PO = 575;

    //status antrian jkn
    const STATUS_DONE_ADMISI = '3';
    const STATUS_TUNGGU_POLI = '4';
    const STATUS_PULANG_POLI = '5';
    const JENIS_RESERVASI_JKN = '1102';
    const JENIS_RESERVASI_APPOINMENT = '1198';
    const JENIS_RESERVASI_SIRS = '622';

    // LOOKUP TRANSAKSI
    const KABAG_KEUANGAN = 'jabatan_kabag_keuangan';
    // LOOKUP TRANSAKSI RUANGAN
    const RUANGAN_IGD = 'ruangan_igd';

    // STATUS PENGAJUAN KLAIM
    const BELUM_MELAKUKAN_PENGAJUAN = 1120;

    const KONFIG_FARMASI_FREETEXT = 1;
    const CACHE_KELAS_PELAYANAN = 'get-kelas-pelayanan';

    // Status Fisio
    const STATUS_FISIO_BELUM_PERIKSA = '1121';
    const STATUS_FISIO_BELUM_BAYAR = '1122';
    const STATUS_FISIO_SELESAI = '1123';

    // Status Order Fisio (Id)
    const STATUS_ORDER_FISIO_CLOSE = 1170;
    const STATUS_ORDER_FISIO_OPEN = 1171;
    const STATUS_ORDER_FISIO_BATAL = 1172;
    const STATUS_ORDER_FISIO_EXPIRE = 1202;
    const STATUS_ORDER_FISIO_DROP_OUT = 1210;

    const INSTALASI_RI = 'instalasi_ri';
    const INSTALASI_RJ = 'instalasi_rj';
    const INSTALASI_FISIO = 'instalasi_fisio';

    const TINDAKAN_KELOMPOK_FISIO = 'kelompok_tindakan_fisio';
    const TINDAKAN_KATEGORI_FISIO = 'kategori_tindakan_fisio';
    const STATUS_BATAL_PROGRAM_FISIO = 'status_batal_program_fisio';
    const STATUS_OPEN_PROGRAM_FISIO = 'status_open_program_fisio';
    const STATUS_CLOSE_PROGRAM_FISIO = 'status_close_program_fisio';

    const KELOMPOK_PEMERIKSAAN_FISIO_DEFAULT = 'kelompok_pemeriksaan_fisio_default';
    const LT_MAKS_FREKUENSI_NON_PAKET = 'konfig_fisio_maks_frekuensi_non_paket';

    // status so barang
    const BELUM_INPUT_HASIL = '2';
    const BELUM_VERIFIKASI = '0';
    const SUDAH_VERIFIKASI = '1';

    const STATUS_PERIKSA_BATAL = 'status_periksa_batal';
    const LT_STATUS_PERIKSA_PULANG = 'status_periksa_pulang';
    const LT_STATUS_PERIKSA_BATAL_KUNJUNGAN = 'status_periksa_batal_kunjungan';

    /** Jenis Pasien JKN */
    const J_P_JKN = 1100;
    const J_P_NONJKN = 1101;

    /** Jenis Rujukan JKN */
    const RUJUK_FKTP_JKN = 1096;

    const RUJUK_RS_JKN = 1099;

    const RUJUK_KONTROL = 1098;

    const RUJUK_INTERNAL = 1097;

    /** Jenis Kunjungan JKN */
    const JNS_KUNJUNGAN_JKN = [
        self::RUJUK_FKTP_JKN => 1,
        self::RUJUK_INTERNAL => 2,
        self::RUJUK_KONTROL => 3,
        self::RUJUK_RS_JKN => 4,
    ];

    //Jenis Recana Kontrol/Inap
    const STS_RECANA_KONTROL = 1125;
    const STS_RENCAN_INAP = 1126;

    //Status Metode Pembayaran Non Tunai
    const PEMBAYARAN_CREDIT_CARD = 683;
    const PEMBAYARAN_BANK_TRANSFER = 684;
    const PEMBAYARAN_DEBIT_CARD = 685;
    // LOOKUP TRANSAKSI
    const LT_FARMASI_UTAMA = 'farmasi_utama';
    const LT_GUDANG_FARMASI = 'gudang_farmasi';
    const LT_COUNTDOWN_EXPIRE_PROGRAM_FISIOTERAPI = 'countdown_expire_program_fisioterapi';
    const LT_STATUS_PROGRAM_EXPIRED = 'status_expired_program_fisio';

    //Tipe Perubahan
    const TIPE_TAMBAH_PAKETTINDAKAN = 1;
    const TIPE_HAPUS_PAKETTINDAKAN = 2;
    const TIPE_TAMBAH_AKOMODASI = 5;
    const TIPE_EDIT_AKOMODASI = 6;
    const TIPE_HAPUS_AKOMODASI = 7;
    const TIPE_EDIT_TANGGAL = 8;
    const TIPE_EDIT_HARGA = 9;
    const TIPE_EDIT_KELAS = 10;
    const TIPE_TAMBAH_OBAT = 11;
    const TIPE_HAPUS_OBAT = 12;
    const TIPE_TAMBAH_PAKET = 13;
    const TIPE_HAPUS_PAKET = 14;
    const TIPE_EDIT_PAKET = 15;
    //STATUS PERIKSA VIEW infopasienbelumbayar_v
    const BATAL_PERIKSA_INFO_PASIEN_BELUM_BAYAR = "402";

    // JENIS STOK OPNAME
    CONST SO_PENYESUAIAN = 138;
    CONST SO_STOK_AWAL = 139;

    const LA_AKSI_VALIDASI = 'validasi';
    const ALASAN_VALIDASI = 'Validasi Ulang';

    const KELOMPOK_MEDIS = 1; // Dokter
    const KELOMPOK_KEPERAWATAN = 2; //Perawat
    const KELOMPOK_MEDIS_NAMA = 'Tenaga Medis'; // Dokter
    const KELOMPOK_KEPERAWATAN_NAMA = 'Tenaga Keperawatan'; //Perawat

    // Lookup Status Kunjungan Fisioterapi
    const STATUS_KUNJUNGAN_FISIO_BELUM_REALISASI = 1206;
    const STATUS_KUNJUNGAN_FISIO_REALISASI = 1207;
    const STATUS_KUNJUNGAN_FISIO_KETIDAKHADIRAN = 1208;
    const STATUS_KUNJUNGAN_FISIO_DROP_OUT = 1209;
    const STATUS_PROGRAM_FISIO_DROP_OUT = 1210;

    const LT_CARA_KELUAR_ATAS_PERSETUJUAN_DOKTER = 'cara_keluar_atas_persetujuan_dokter';
    /* kelompok tindakan id karcis */
    const KELOMPOK_TINDAKAN_ID = 'kelompok_tindakan_id';

    // etiket template racikan
    const SINGLE_PAGE = 'single_page';
    const MULTI_PAGE = 'multi_page';

    /* cache pendaftaran penjamin */
    const PENDAFTARAN_PENJAMIN = 'pendaftaran-penjamin';

    /**
     * @function : Set JK Claim
     */
    const LAKI = 'L';
    const PEREMPUAN = 'P';
    const JENIS_LAKI = 1;
    const JENIS_PEREMPUAN = 2;

    /**
     * @function : Set Jenis Instalasi Claim
     */
    const CLAIM_RAJAL = 2;
    const CLAIM_RANAP = 1;

    const LOOKUP_BPJS_LIVE = 'bpjs_live';
    const KODE_TARIF_BPJS = 'kode_tarifbpjs';

     /* SOAP RJ V */
     const JENIS_SOAP = 'TINDAKAN';
     const GROUPING_TIPE = 'SOAP FISIOTERAPI RJ';

     /* Instalasi ID */
     const INSTALASI_ID_RADIOLOGI = 5;

     const STATUS_EXPERTISE_RADIOLOGI = 'status_periksa_expertise';
     const STATUS_EXPERTISE_SUDAH = '1306';
     const STATUS_EXPERTISE_BELUM = '1307';

    const LAPORAN_PEMBELIAN = 1;
    const LAPORAN_PENGELUARAN_KASIR = 2;
    const LAPORAN_KAS_HARIAN = 3;
    const LAPORAN_PENERIMAAN_HARIAN = 4;
    const REKAP_LAPORAN_KASIR = 5;


     const LIST_LAPORAN_KASIR = [
        self::LAPORAN_PEMBELIAN => 'Laporan Pembelian',
        self::LAPORAN_PENGELUARAN_KASIR => 'Laporan penerimaan dan pengeluaran kasir',
        self::LAPORAN_KAS_HARIAN => 'Laporan kas Harian',
        self::LAPORAN_PENERIMAAN_HARIAN => 'Laporan Penerimaan Harian',
        self::REKAP_LAPORAN_KASIR => 'Rekap Laporan Kasir',
    ];

    const LIST_ROUTING_KEYS_LAPORAN_KASIR = [
        self::LAPORAN_PEMBELIAN => 'laporan_pembelian',
        self::LAPORAN_PENGELUARAN_KASIR => 'laporan_pengeluaran_kasir',
        self::LAPORAN_KAS_HARIAN => 'laporan_kas_harian',
        self::LAPORAN_PENERIMAAN_HARIAN => 'laporan_penerimaan_harian',
        self::REKAP_LAPORAN_KASIR => 'rekap_laporan_kasir',
    ];

    const VAR_CACHE_LAPORAN_KASIR = 'var_cache_laporan_kasir';
    const VAR_LAPORAN_KASIR = 'laporan_kasir';
    const STRING_IS_ALERGI = 'Ya';
    const STRING_ALERGI_OBAT = 'Obat';
    const STRING_ALERGI_MAKANAN = 'Makanan';
    const STRING_ALERGI_LAINNYA = 'Lainnya';

    const CARABAYAR_APOTEK_ONLINE = 'carabayar_apotek_online';
    const KRONIS_LIMIT = 'kronis_limit';
    
    /* STATUS RUJUKAN BPJS */
    const RUJUKAN_BPJS_LAMA = '1317';
    const RUJUKAN_BPJS_BARU = '1318';

    /* STATUS Rencana Kontrol BPJS */
    const RK_JNS_KONTROL_SPRI = 1 ; //rencana kontrol rawat inap / spri
    const RK_JNS_KONTROL_RK = 2 ; //rencana kontrol
    
    /* Tipe Pasien Pendaftaran */
    const TIPE_PASIEN_PENDAFTARAN_LAMA = 0;
    const TIPE_PASIEN_PENDAFTARAN_BARU = 1;

    /* KATEGORI RESEP RAWAT INAP */
    const KATEGORI_RESEP_UDD = 2115;
    const KATEGORI_RESEP_TERAPI_BARU = 2116;
    const KATEGORI_RESEP_OBAT_PULANG = 2117;

    const NAMA_KATEGORI_RESEP = [
        self::KATEGORI_RESEP_UDD => 'Resep UDD',
        self::KATEGORI_RESEP_TERAPI_BARU => 'Terapi Baru',
        self::KATEGORI_RESEP_OBAT_PULANG => 'Obat Pulang'
    ];

    /* RETUR*/
    const RETUR_BELUM_VERIFIKASI = 2118;
    const RETUR_VERIFIKASI = 2119;
    const BATAL_RETUR = 2120;
    /** Antrian BPJS */
    const ANTRIAN_BPJS = 2121;
    /* Unduh Dokumen */
    const ON_PROGRES_UNDUH_DOKUMEN = 2122;
    const SELESAI_UNDUH_DOKUMN = 2123;
    const BELUM_UNDUH_DOKUMEN = 2172;
    const VAR_CACHE_LAPORAN_DAN_KPI = 'var_cache_laporan_dan_kpi';
    const VAR_LAPORAN_DAN_KPI = 'laporan_dan_kpi';

    const TYPE_CREATE_APLICARE = 'create';
    const TYPE_UPDATE_APLICARE = 'update';

    const LOOKUP_RS_ONLINE = 'rs_online';

    /**
     * Status persetujuan otoritas penjamin
     */
    const STATUS_APPROVED = 1324;
    const STATUS_REJECT = 1325;
    const STATUS_PENDING = 1323;
    
    /* Konfig Lookuptransaksi */
    const KONFIG_FLOW_TASKID = 'flow_taskid_alur_jam_pelayanan';
    const KONFIG_TOLAK_HADIR_RESERVASI = 'tolak_hadir_tanpa_validasi_tanggal';

    // bagian tubuh detail
    const BAGIAN_TUBUH_GIGI = 8;
    const BAGIAN_TUBUH_ABDOMEN = 13;
    
    // status verifikasi pasien bpjs
    const STATUS_VERIFIKASI_BPJS_BLM = 549;
    const STATUS_VERIFIKASI_BPJS_SDH = 550;
    const STATUS_VERIFIKASI_BPJS_PRS = 556;
    const STATUS_VERIFIKASI_BPJS_FNL = 551;

    // Konfig upload dokumen
    const CPPT_RANAP = 9;
    const KONFIG_DOKUMEN = 'konfig_dokumen';

    // kategori triase
    const KATEGORI_TRIASE_LESS_URGENT = 'less_urgent';
    // produksi Obat
    const RUANGAN_GUDANG_FARMASI = 25;
    const BELUM_VERIFIKASI_PESANAN = 2183;
    const SUDAH_VERIFIKASI_PESANAN = 2184;
    const BATAL_PEMESANAN = 2185;

    // produksi Obat
    const PRODUKSI_OBAT = 2189;
    const PRODUKSI = 2186;
    const DEFINE_MATERIAL = 2187;
    const BATAL_PRODUKSI = 2188;

    const CRON_HIDE_PASIEN_MENINGGAL = 'konfig_cron_hide_pasien_meninggal';

    //status dokumen esign
    const ESIGN_STAT_GENERATED = 'GENERATED';
    const ESIGN_STAT_SIGNED = 'SIGNED';
    // ONLY TILAKA
    const ESIGN_STAT_SIGNING_TK = 'SIGNING'; // AFTER REQUEST
    const ESIGN_STAT_EXECUTED_TK = 'EXECUTED'; // AFTER EXECUTE

    // Keterangan log barang (bukan lookup id)
    const KET_HRG_BRG_PM = 'Perubahan Manual';
    const KET_HRG_BRG_PBS = 'Penerimaan Barang Supplier';
    const KET_HRG_BRG_AB = 'Adjusment Barang';
    const KET_HRG_BRG_PBM = 'Penerimaan Barang';
    
    const JENIS_OBAT_BPJS = 'jenis_obat_bpjs';
    const JENIS_OBAT_BPJS_KRONIS = 4031;
    const JENIS_OBAT_BPJS_PRB = 4032;
    const JENIS_OBAT_BPJS_KEMO = 4033;

    // sumber data ttv
    const ASESMEN_KEPERAWATAN = 'asesmen_keperawatan';
    const ASESMEN_MEDIS = 'asesmen_medis';
    const MONITORING_TTV = 'monitoring_ttv';
    const RUANGAN_FISIOTERAPI = 935;

    const JENIS_EWS_KEBIDANAN = 2256;
    const JENIS_EWS_ANAK = 2258;
    const JENIS_EWS_DEWASA = 2257;
    const JENIS_EWS_IBU_HAMIL = 2259;
    const SUMBER_TTV_EWS = 2264;
    const SUMBER_TTV_SBAR = 2266;
    const JENIS_PENDAFTARAN_RANAP = 'ranap';

    // LOOKUP VERIFIKASI BANTARAN
    const BELUM_VERIFIKASI_BANTARAN = 4035;
    const SUDAH_VERIFIKASI_BANTARAN = 4036;
    const TOLAK_VERFIKASI_BANTARAN = 4037;

    // LOOKUP STATUS PELAYANAN BANTARAN
    const DALAM_PELAYANAN_BANTARAN = 4038;
    const SELESAI_PELAYANAN_BANTARAN = 4039;
    const MENUNGGU_DIDAFTARKAN_BANTARAN = 4040;
}
